<?php

namespace humhub\modules\stewardship\models;

use humhub\components\ActiveRecord;
use yii\db\ActiveQuery;

class Allocation extends ActiveRecord
{
    public static function tableName() { return 'stewardship_allocation'; }

    public function rules()
    {
        return [
            [['transaction_id', 'grant_id', 'program_name', 'amount'], 'required'],
            [['transaction_id', 'grant_id'], 'integer'],
            [['program_name'], 'string', 'max' => 255],
            [['amount'], 'number', 'min' => 0.01],
            [['percentage'], 'number', 'min' => 0, 'max' => 100],
        ];
    }

    public function getTransaction(): ActiveQuery { return $this->hasOne(Transaction::class, ['id' => 'transaction_id']); }
    public function getGrant(): ActiveQuery { return $this->hasOne(Grant::class, ['id' => 'grant_id']); }
}
