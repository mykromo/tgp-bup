<?php

namespace humhub\modules\shop\models;

use humhub\components\ActiveRecord;
use yii\db\ActiveQuery;

class FavoriteStore extends ActiveRecord
{
    public static function tableName() { return 'shop_favorite_store'; }

    public function rules()
    {
        return [
            [['user_id', 'vendor_id'], 'required'],
            [['user_id', 'vendor_id'], 'integer'],
        ];
    }

    public function getVendor(): ActiveQuery { return $this->hasOne(Vendor::class, ['id' => 'vendor_id']); }

    public static function isFavorited(int $userId, int $vendorId): bool
    {
        return static::find()->where(['user_id' => $userId, 'vendor_id' => $vendorId])->exists();
    }

    public static function toggle(int $userId, int $vendorId): bool
    {
        $existing = static::findOne(['user_id' => $userId, 'vendor_id' => $vendorId]);
        if ($existing) {
            $existing->delete();
            return false;
        }
        $f = new static();
        $f->user_id = $userId;
        $f->vendor_id = $vendorId;
        $f->created_at = date('Y-m-d H:i:s');
        $f->save(false);
        return true;
    }
}
