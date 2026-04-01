<?php

namespace humhub\modules\stewardship\models;

use humhub\components\ActiveRecord;
use Yii;
use yii\db\ActiveQuery;

class Grant extends ActiveRecord
{
    public static function tableName() { return 'stewardship_grant'; }

    public function rules()
    {
        return [
            [['space_id', 'fund_id', 'name', 'amount_awarded'], 'required'],
            [['space_id', 'fund_id'], 'integer'],
            [['name', 'grantor'], 'string', 'max' => 255],
            [['amount_awarded', 'amount_spent'], 'number', 'min' => 0],
            [['status'], 'in', 'range' => ['active', 'closed', 'expired']],
            [['reporting_frequency'], 'in', 'range' => ['monthly', 'quarterly', 'annually']],
            [['start_date', 'end_date'], 'safe'],
            [['notes'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('StewardshipModule.base', 'Grant Name'),
            'grantor' => Yii::t('StewardshipModule.base', 'Grantor'),
            'amount_awarded' => Yii::t('StewardshipModule.base', 'Amount Awarded'),
            'amount_spent' => Yii::t('StewardshipModule.base', 'Amount Spent'),
            'start_date' => Yii::t('StewardshipModule.base', 'Start Date'),
            'end_date' => Yii::t('StewardshipModule.base', 'End Date'),
            'reporting_frequency' => Yii::t('StewardshipModule.base', 'Reporting Frequency'),
        ];
    }

    public function getFund(): ActiveQuery { return $this->hasOne(Fund::class, ['id' => 'fund_id']); }
    public function getTransactions(): ActiveQuery { return $this->hasMany(Transaction::class, ['grant_id' => 'id']); }
    public function getAllocations(): ActiveQuery { return $this->hasMany(Allocation::class, ['grant_id' => 'id']); }

    public function getAmountRemaining(): float
    {
        return (float) $this->amount_awarded - (float) $this->amount_spent;
    }

    public function getUtilizationPercent(): float
    {
        return $this->amount_awarded > 0 ? round(($this->amount_spent / $this->amount_awarded) * 100, 1) : 0;
    }

    public function recalculateSpent(): void
    {
        $spent = (float) Transaction::find()
            ->where(['grant_id' => $this->id, 'type' => 'expense', 'is_voided' => 0])
            ->sum('amount') ?: 0;
        $this->updateAttributes(['amount_spent' => $spent]);
    }
}
