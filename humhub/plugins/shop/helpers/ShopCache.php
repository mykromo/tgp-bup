<?php

namespace humhub\modules\shop\helpers;

use humhub\modules\shop\models\Category;
use humhub\modules\shop\models\Product;
use humhub\modules\shop\models\Vendor;
use Yii;

class ShopCache
{
    const DEFAULT_TTL = 300; // 5 minutes

    public static function getTtl(): int
    {
        $module = Yii::$app->getModule('shop');
        if ($module && $module->settings) {
            $ttl = $module->settings->get('cacheTtl');
            if ($ttl !== null) return (int) $ttl;
        }
        return self::DEFAULT_TTL;
    }

    public static function getCategories(): array
    {
        return Yii::$app->cache->getOrSet('shop_categories', function () {
            return Category::find()->where(['is_active' => 1])->orderBy(['sort_order' => SORT_ASC])->all();
        }, static::getTtl());
    }

    public static function getCategoryMap(): array
    {
        return Yii::$app->cache->getOrSet('shop_category_map', function () {
            return Category::getDropdownList();
        }, static::getTtl());
    }

    public static function getProduct(int $id): ?Product
    {
        return Yii::$app->cache->getOrSet("shop_product_{$id}", function () use ($id) {
            return Product::find()->where(['id' => $id, 'is_active' => 1])->with(['images', 'variants', 'category', 'vendor'])->one();
        }, static::getTtl());
    }

    public static function getVendor(int $id): ?Vendor
    {
        return Yii::$app->cache->getOrSet("shop_vendor_{$id}", function () use ($id) {
            return Vendor::findOne(['id' => $id, 'status' => Vendor::STATUS_APPROVED]);
        }, static::getTtl());
    }

    public static function invalidateProduct(int $id): void
    {
        Yii::$app->cache->delete("shop_product_{$id}");
    }

    public static function invalidateVendor(int $id): void
    {
        Yii::$app->cache->delete("shop_vendor_{$id}");
    }

    public static function invalidateCategories(): void
    {
        Yii::$app->cache->delete('shop_categories');
        Yii::$app->cache->delete('shop_category_map');
    }

    public static function flushAll(): void
    {
        static::invalidateCategories();
    }
}
