<?php

namespace humhub\modules\shop\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use Yii;
use yii\db\ActiveQuery;

class Vendor extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_SUSPENDED = 'suspended';

    public static function tableName() { return 'shop_vendor'; }

    public function rules()
    {
        return [
            [['user_id', 'shop_name'], 'required'],
            [['user_id', 'reviewed_by'], 'integer'],
            [['shop_name'], 'string', 'max' => 255],
            [['description', 'rejection_reason'], 'string'],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_SUSPENDED]],
            [['reviewed_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'shop_name' => Yii::t('ShopModule.base', 'Shop Name'),
            'description' => Yii::t('ShopModule.base', 'Shop Description'),
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');
        return parent::beforeSave($insert);
    }

    public function getUser(): ActiveQuery { return $this->hasOne(User::class, ['id' => 'user_id']); }
    public function getReviewer(): ActiveQuery { return $this->hasOne(User::class, ['id' => 'reviewed_by']); }
    public function getDocuments(): ActiveQuery { return $this->hasMany(VendorDocument::class, ['vendor_id' => 'id']); }
    public function getProducts(): ActiveQuery { return $this->hasMany(Product::class, ['vendor_id' => 'id']); }

    public function isApproved(): bool { return $this->status === self::STATUS_APPROVED; }
    public function isPending(): bool { return $this->status === self::STATUS_PENDING; }

    public static function getForUser(int $userId): ?self
    {
        return static::find()->where(['user_id' => $userId])->andWhere(['!=', 'status', self::STATUS_REJECTED])->one();
    }

    public static function getForUserIncludingRejected(int $userId): ?self
    {
        return static::findOne(['user_id' => $userId]);
    }

    public static function isUserApproved(int $userId): bool
    {
        return static::find()->where(['user_id' => $userId, 'status' => self::STATUS_APPROVED])->exists();
    }

    public static function getStatusLabels(): array
    {
        return [
            self::STATUS_PENDING => Yii::t('ShopModule.base', 'Pending Review'),
            self::STATUS_APPROVED => Yii::t('ShopModule.base', 'Approved'),
            self::STATUS_REJECTED => Yii::t('ShopModule.base', 'Rejected'),
            self::STATUS_SUSPENDED => Yii::t('ShopModule.base', 'Suspended'),
        ];
    }

    public static function getStatusBadge(string $status): string
    {
        $map = [
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_SUSPENDED => 'warning',
            self::STATUS_PENDING => 'info',
        ];
        return $map[$status] ?? 'default';
    }

    public static function getRequiredDocuments(): array
    {
        return [
            'valid_id' => Yii::t('ShopModule.base', 'Valid Government ID'),
            'business_permit' => Yii::t('ShopModule.base', 'Business Permit / DTI Registration'),
            'proof_of_address' => Yii::t('ShopModule.base', 'Proof of Address'),
        ];
    }
}
