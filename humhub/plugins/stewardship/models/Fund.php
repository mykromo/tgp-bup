<?php

namespace humhub\modules\stewardship\models;

use humhub\components\ActiveRecord;
use Yii;
use yii\db\ActiveQuery;

class Fund extends ActiveRecord
{
    const TYPE_UNRESTRICTED = 'unrestricted';
    const TYPE_TEMPORARILY_RESTRICTED = 'temporarily_restricted';
    const TYPE_PERMANENTLY_RESTRICTED = 'permanently_restricted';

    public static function tableName() { return 'stewardship_fund'; }

    public function rules()
    {
        return [
            [['space_id', 'name', 'fund_type'], 'required'],
            [['space_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['fund_type'], 'in', 'range' => [self::TYPE_UNRESTRICTED, self::TYPE_TEMPORARILY_RESTRICTED, self::TYPE_PERMANENTLY_RESTRICTED]],
            [['description', 'restriction_purpose'], 'string'],
            [['balance'], 'number'],
            [['is_active'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('StewardshipModule.base', 'Fund Name'),
            'fund_type' => Yii::t('StewardshipModule.base', 'Fund Type'),
            'description' => Yii::t('StewardshipModule.base', 'Description'),
            'restriction_purpose' => Yii::t('StewardshipModule.base', 'Restriction Purpose'),
            'balance' => Yii::t('StewardshipModule.base', 'Balance'),
        ];
    }

    public static function getTypeLabels(): array
    {
        return [
            self::TYPE_UNRESTRICTED => Yii::t('StewardshipModule.base', 'Unrestricted'),
            self::TYPE_TEMPORARILY_RESTRICTED => Yii::t('StewardshipModule.base', 'Temporarily Restricted'),
            self::TYPE_PERMANENTLY_RESTRICTED => Yii::t('StewardshipModule.base', 'Permanently Restricted'),
        ];
    }

    public function getTransactions(): ActiveQuery
    {
        return $this->hasMany(Transaction::class, ['fund_id' => 'id']);
    }

    public function getGrants(): ActiveQuery
    {
        return $this->hasMany(Grant::class, ['fund_id' => 'id']);
    }

    public function isRestricted(): bool
    {
        return $this->fund_type !== self::TYPE_UNRESTRICTED;
    }

    public function recalculateBalance(): void
    {
        $income = (float) Transaction::find()
            ->where(['fund_id' => $this->id, 'is_voided' => 0])
            ->andWhere(['in', 'type', ['income', 'transfer_in']])
            ->sum('amount') ?: 0;
        $expense = (float) Transaction::find()
            ->where(['fund_id' => $this->id, 'is_voided' => 0])
            ->andWhere(['in', 'type', ['expense', 'transfer_out']])
            ->sum('amount') ?: 0;
        $this->updateAttributes(['balance' => $income - $expense]);
    }
}
