<?php

namespace humhub\modules\shop\models;

use humhub\components\ActiveRecord;
use Yii;

class Product extends ActiveRecord
{
    public static function tableName() { return 'shop_product'; }

    public function rules()
    {
        return [
            [['name', 'price'], 'required'],
            [['space_id', 'stock', 'sort_order'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['price'], 'number', 'min' => 0],
            [['currency'], 'string', 'max' => 10],
            [['image_url'], 'string', 'max' => 500],
            [['is_active'], 'boolean'],
            [['sort_order'], 'default', 'value' => 0],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('ShopModule.base', 'Product Name'),
            'description' => Yii::t('ShopModule.base', 'Description'),
            'price' => Yii::t('ShopModule.base', 'Price'),
            'stock' => Yii::t('ShopModule.base', 'Stock'),
            'image_url' => Yii::t('ShopModule.base', 'Image URL'),
            'is_active' => Yii::t('ShopModule.base', 'Active'),
        ];
    }

    public function isInStock(): bool
    {
        return $this->stock === null || $this->stock > 0;
    }

    public function formatPrice(): string
    {
        return '₱' . number_format((float) $this->price, 2);
    }

    public function getImages(): \yii\db\ActiveQuery
    {
        return $this->hasMany(ProductImage::class, ['product_id' => 'id'])->orderBy(['sort_order' => SORT_ASC]);
    }

    public function getFirstImageUrl(): ?string
    {
        $img = $this->getImages()->one();
        return $img ? $img->getUrl() : ($this->image_url ?: null);
    }
}
