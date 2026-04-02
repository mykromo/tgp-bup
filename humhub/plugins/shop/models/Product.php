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
            [['space_id', 'vendor_id', 'category_id', 'stock', 'sort_order'], 'integer'],
            [['name', 'location'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['price', 'sale_price'], 'number', 'min' => 0],
            [['currency'], 'string', 'max' => 10],
            [['image_url'], 'string', 'max' => 500],
            [['is_active'], 'boolean'],
            [['sort_order'], 'default', 'value' => 0],
            [['sale_start', 'sale_end'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('ShopModule.base', 'Product Name'),
            'description' => Yii::t('ShopModule.base', 'Description'),
            'price' => Yii::t('ShopModule.base', 'Price'),
            'sale_price' => Yii::t('ShopModule.base', 'Sale Price'),
            'stock' => Yii::t('ShopModule.base', 'Stock'),
            'category_id' => Yii::t('ShopModule.base', 'Category'),
            'location' => Yii::t('ShopModule.base', 'Location'),
            'is_active' => Yii::t('ShopModule.base', 'Active'),
        ];
    }

    public function isInStock(): bool
    {
        return $this->stock === null || $this->stock > 0;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        \humhub\modules\shop\helpers\ShopCache::invalidateProduct($this->id);
        if ($this->vendor_id) {
            \humhub\modules\shop\helpers\ShopCache::invalidateVendor($this->vendor_id);
        }
    }

    public function formatPrice(): string
    {
        if ($this->isOnSale()) {
            return '<s class="text-muted">₱' . number_format((float) $this->price, 2) . '</s> <span class="text-danger">₱' . number_format((float) $this->sale_price, 2) . '</span>';
        }
        return '₱' . number_format((float) $this->price, 2);
    }

    public function getEffectivePrice(): float
    {
        return $this->isOnSale() ? (float) $this->sale_price : (float) $this->price;
    }

    public function isOnSale(): bool
    {
        if (!$this->sale_price || $this->sale_price >= $this->price) return false;
        $now = time();
        if ($this->sale_start && strtotime($this->sale_start) > $now) return false;
        if ($this->sale_end && strtotime($this->sale_end) < $now) return false;
        return true;
    }

    public function getCategory(): \yii\db\ActiveQuery { return $this->hasOne(Category::class, ['id' => 'category_id']); }
    public function getVendor(): \yii\db\ActiveQuery { return $this->hasOne(Vendor::class, ['id' => 'vendor_id']); }
    public function getVariants(): \yii\db\ActiveQuery { return $this->hasMany(ProductVariant::class, ['product_id' => 'id'])->andWhere(['is_active' => 1]); }

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
