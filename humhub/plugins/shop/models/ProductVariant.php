<?php

namespace humhub\modules\shop\models;

use humhub\components\ActiveRecord;
use yii\db\ActiveQuery;

class ProductVariant extends ActiveRecord
{
    public static function tableName() { return 'shop_product_variant'; }

    public function rules()
    {
        return [
            [['product_id', 'name'], 'required'],
            [['product_id', 'stock'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['sku'], 'string', 'max' => 100],
            [['price_adjustment'], 'number'],
            [['is_active'], 'boolean'],
        ];
    }

    public function getProduct(): ActiveQuery { return $this->hasOne(Product::class, ['id' => 'product_id']); }

    public function getEffectivePrice(): float
    {
        $base = $this->product ? (float) $this->product->price : 0;
        return $base + (float) $this->price_adjustment;
    }

    public function formatPrice(): string
    {
        return '₱' . number_format($this->getEffectivePrice(), 2);
    }

    public function isInStock(): bool
    {
        return $this->stock === null || $this->stock > 0;
    }
}
