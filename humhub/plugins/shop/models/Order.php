<?php

namespace humhub\modules\shop\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use Yii;
use yii\db\ActiveQuery;

class Order extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_VERIFIED = 'verified';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    public static function tableName() { return 'shop_order'; }

    public function rules()
    {
        return [
            [['space_id', 'user_id', 'total_amount'], 'required'],
            [['space_id', 'user_id', 'verified_by'], 'integer'],
            [['total_amount'], 'number'],
            [['payment_reference'], 'string', 'max' => 255],
            [['payment_method'], 'string', 'max' => 100],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_PAID, self::STATUS_VERIFIED, self::STATUS_CANCELLED, self::STATUS_REFUNDED]],
            [['notes', 'buyer_name', 'buyer_email'], 'string'],
            [['payment_date', 'verified_at'], 'safe'],
        ];
    }

    public function getUser(): ActiveQuery { return $this->hasOne(User::class, ['id' => 'user_id']); }
    public function getItems(): ActiveQuery { return $this->hasMany(OrderItem::class, ['order_id' => 'id']); }
    public function getVerifier(): ActiveQuery { return $this->hasOne(User::class, ['id' => 'verified_by']); }

    public function beforeSave($insert)
    {
        if ($insert && !$this->order_number) {
            $this->order_number = 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8));
        }
        if (!$this->created_at && $insert) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        $this->updated_at = date('Y-m-d H:i:s');
        return parent::beforeSave($insert);
    }

    public function formatTotal(): string
    {
        return '₱' . number_format((float) $this->total_amount, 2);
    }

    public static function getStatusLabels(): array
    {
        return [
            self::STATUS_PENDING => Yii::t('ShopModule.base', 'Pending Payment'),
            self::STATUS_PAID => Yii::t('ShopModule.base', 'Paid (Unverified)'),
            self::STATUS_VERIFIED => Yii::t('ShopModule.base', 'Verified'),
            self::STATUS_CANCELLED => Yii::t('ShopModule.base', 'Cancelled'),
            self::STATUS_REFUNDED => Yii::t('ShopModule.base', 'Refunded'),
        ];
    }

    public static function getStatusBadge(string $status): string
    {
        $map = ['pending' => 'warning', 'paid' => 'info', 'verified' => 'success', 'cancelled' => 'default', 'refunded' => 'danger'];
        return $map[$status] ?? 'default';
    }
}
