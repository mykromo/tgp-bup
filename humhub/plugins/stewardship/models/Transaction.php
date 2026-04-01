<?php

namespace humhub\modules\stewardship\models;

use humhub\components\ActiveRecord;
use Yii;
use yii\db\ActiveQuery;

class Transaction extends ActiveRecord
{
    const TYPE_INCOME = 'income';
    const TYPE_EXPENSE = 'expense';
    const TYPE_TRANSFER_IN = 'transfer_in';
    const TYPE_TRANSFER_OUT = 'transfer_out';

    const FUNC_PROGRAM = 'program';
    const FUNC_MANAGEMENT = 'management';
    const FUNC_FUNDRAISING = 'fundraising';

    public static function tableName() { return 'stewardship_transaction'; }

    public function rules()
    {
        return [
            [['space_id', 'fund_id', 'type', 'amount', 'description', 'transaction_date'], 'required'],
            [['space_id', 'fund_id', 'grant_id'], 'integer'],
            [['amount'], 'number', 'min' => 0.01],
            [['type'], 'in', 'range' => [self::TYPE_INCOME, self::TYPE_EXPENSE, self::TYPE_TRANSFER_IN, self::TYPE_TRANSFER_OUT]],
            [['functional_category'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 500],
            [['reference', 'program_name'], 'string', 'max' => 255],
            [['transaction_date'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fund_id' => Yii::t('StewardshipModule.base', 'Fund'),
            'grant_id' => Yii::t('StewardshipModule.base', 'Grant'),
            'type' => Yii::t('StewardshipModule.base', 'Type'),
            'amount' => Yii::t('StewardshipModule.base', 'Amount'),
            'description' => Yii::t('StewardshipModule.base', 'Description'),
            'reference' => Yii::t('StewardshipModule.base', 'Reference #'),
            'functional_category' => Yii::t('StewardshipModule.base', 'Functional Category'),
            'program_name' => Yii::t('StewardshipModule.base', 'Program'),
            'transaction_date' => Yii::t('StewardshipModule.base', 'Date'),
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            // Validate restricted fund spending
            $fund = Fund::findOne($this->fund_id);
            if ($fund && $fund->isRestricted() && $this->type === self::TYPE_EXPENSE) {
                if ($this->program_name && $fund->restriction_purpose
                    && stripos($fund->restriction_purpose, $this->program_name) === false) {
                    $this->addError('fund_id', Yii::t('StewardshipModule.base',
                        'This expense does not match the restriction purpose of the fund.'));
                    return false;
                }
            }
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        // Recalculate fund balance
        $fund = Fund::findOne($this->fund_id);
        if ($fund) {
            $fund->recalculateBalance();
        }
        // Update grant spent amount
        if ($this->grant_id && $this->type === self::TYPE_EXPENSE) {
            $grant = Grant::findOne($this->grant_id);
            if ($grant) {
                $grant->recalculateSpent();
            }
        }
        // Audit log
        if ($insert) {
            AuditLog::log($this->space_id, 'transaction', $this->id, 'created');
        }
    }

    public function getFund(): ActiveQuery { return $this->hasOne(Fund::class, ['id' => 'fund_id']); }
    public function getGrant(): ActiveQuery { return $this->hasOne(Grant::class, ['id' => 'grant_id']); }

    public static function getTypeLabels(): array
    {
        return [
            self::TYPE_INCOME => Yii::t('StewardshipModule.base', 'Income'),
            self::TYPE_EXPENSE => Yii::t('StewardshipModule.base', 'Expense'),
            self::TYPE_TRANSFER_IN => Yii::t('StewardshipModule.base', 'Transfer In'),
            self::TYPE_TRANSFER_OUT => Yii::t('StewardshipModule.base', 'Transfer Out'),
        ];
    }

    public static function getFunctionalLabels(?int $spaceId = null): array
    {
        if ($spaceId) {
            return FunctionalCategory::getActiveMap($spaceId);
        }
        // Fallback defaults
        return [
            'program' => Yii::t('StewardshipModule.base', 'Program Services'),
            'management' => Yii::t('StewardshipModule.base', 'Management & General'),
            'fundraising' => Yii::t('StewardshipModule.base', 'Fundraising'),
        ];
    }

    public function void(string $reason): bool
    {
        if ($this->is_voided) return false;
        $this->is_voided = 1;
        $this->voided_by = Yii::$app->user->id;
        $this->voided_at = date('Y-m-d H:i:s');
        $this->void_reason = $reason;
        $result = $this->save(false);
        $fund = Fund::findOne($this->fund_id);
        if ($fund) $fund->recalculateBalance();
        AuditLog::log($this->space_id, 'transaction', $this->id, 'voided', 'void_reason', null, $reason);
        return $result;
    }
}
