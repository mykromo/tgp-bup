<?php

namespace humhub\modules\shop\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use yii\db\ActiveQuery;

class OrderRequest extends ActiveRecord
{
    const TYPE_UPDATE = 'update';
    const TYPE_CANCEL = 'cancel';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public static function tableName() { return 'shop_order_request'; }

    public function rules()
    {
        return [
            [['order_id', 'user_id', 'type'], 'required'],
            [['order_id', 'user_id', 'new_address_id', 'new_quantity'], 'integer'],
            [['type'], 'in', 'range' => [self::TYPE_UPDATE, self::TYPE_CANCEL]],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED]],
            [['details', 'seller_response'], 'string'],
            [['responded_at'], 'safe'],
        ];
    }

    public function getOrder(): ActiveQuery { return $this->hasOne(Order::class, ['id' => 'order_id']); }
    public function getUser(): ActiveQuery { return $this->hasOne(User::class, ['id' => 'user_id']); }
    public function getNewAddress(): ActiveQuery { return $this->hasOne(DeliveryAddress::class, ['id' => 'new_address_id']); }

    public function beforeSave($insert)
    {
        if ($insert) $this->created_at = date('Y-m-d H:i:s');
        return parent::beforeSave($insert);
    }

    public static function hasPending(int $orderId): bool
    {
        return static::find()->where(['order_id' => $orderId, 'status' => self::STATUS_PENDING])->exists();
    }
}
