<?php

namespace humhub\modules\shop\models;

use humhub\components\ActiveRecord;
use yii\db\ActiveQuery;

class Wishlist extends ActiveRecord
{
    public static function tableName() { return 'shop_wishlist'; }

    public function rules()
    {
        return [
            [['user_id', 'product_id'], 'required'],
            [['user_id', 'product_id'], 'integer'],
        ];
    }

    public function getProduct(): ActiveQuery { return $this->hasOne(Product::class, ['id' => 'product_id']); }

    public static function isWishlisted(int $userId, int $productId): bool
    {
        return static::find()->where(['user_id' => $userId, 'product_id' => $productId])->exists();
    }

    public static function toggle(int $userId, int $productId): bool
    {
        $existing = static::findOne(['user_id' => $userId, 'product_id' => $productId]);
        if ($existing) {
            $existing->delete();
            return false;
        }
        $w = new static();
        $w->user_id = $userId;
        $w->product_id = $productId;
        $w->created_at = date('Y-m-d H:i:s');
        $w->save(false);
        return true;
    }
}
