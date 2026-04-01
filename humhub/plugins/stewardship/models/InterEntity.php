<?php

namespace humhub\modules\stewardship\models;

use humhub\components\ActiveRecord;
use humhub\modules\space\models\Space;
use Yii;
use yii\db\ActiveQuery;

class InterEntity extends ActiveRecord
{
    public static function tableName() { return 'stewardship_interentity'; }

    public function rules()
    {
        return [
            [['from_space_id', 'to_space_id', 'amount', 'description'], 'required'],
            [['from_space_id', 'to_space_id'], 'integer'],
            [['amount'], 'number', 'min' => 0.01],
            [['description'], 'string', 'max' => 500],
            [['status'], 'in', 'range' => ['pending', 'settled']],
        ];
    }

    public function getFromSpace(): ActiveQuery { return $this->hasOne(Space::class, ['id' => 'from_space_id']); }
    public function getToSpace(): ActiveQuery { return $this->hasOne(Space::class, ['id' => 'to_space_id']); }

    public function settle(int $fromFundId, int $toFundId): bool
    {
        $db = Yii::$app->db;
        $txn = $db->beginTransaction();
        try {
            // Create transfer_out in source chapter
            $out = new Transaction();
            $out->space_id = $this->from_space_id;
            $out->fund_id = $fromFundId;
            $out->type = Transaction::TYPE_TRANSFER_OUT;
            $out->amount = $this->amount;
            $out->description = 'Due To: ' . $this->description;
            $out->transaction_date = date('Y-m-d');
            $out->save();

            // Create transfer_in in target chapter
            $in = new Transaction();
            $in->space_id = $this->to_space_id;
            $in->fund_id = $toFundId;
            $in->type = Transaction::TYPE_TRANSFER_IN;
            $in->amount = $this->amount;
            $in->description = 'Due From: ' . $this->description;
            $in->transaction_date = date('Y-m-d');
            $in->save();

            $this->status = 'settled';
            $this->from_transaction_id = $out->id;
            $this->to_transaction_id = $in->id;
            $this->settled_at = date('Y-m-d H:i:s');
            $this->settled_by = Yii::$app->user->id;
            $this->save(false);

            AuditLog::log($this->from_space_id, 'interentity', $this->id, 'settled');
            $txn->commit();
            return true;
        } catch (\Throwable $e) {
            $txn->rollBack();
            Yii::error('InterEntity settle failed: ' . $e->getMessage(), 'stewardship');
            return false;
        }
    }
}
