<?php

namespace humhub\modules\shop\models;

use humhub\components\ActiveRecord;
use yii\db\ActiveQuery;

class Category extends ActiveRecord
{
    public static function tableName() { return 'shop_category'; }

    public function rules()
    {
        return [
            [['name', 'slug'], 'required'],
            [['name', 'slug'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['slug'], 'match', 'pattern' => '/^[a-z0-9\-]+$/'],
            [['parent_id', 'sort_order'], 'integer'],
            [['is_active'], 'boolean'],
            [['sort_order'], 'default', 'value' => 0],
        ];
    }

    public function getParent(): ActiveQuery { return $this->hasOne(self::class, ['id' => 'parent_id']); }
    public function getChildren(): ActiveQuery { return $this->hasMany(self::class, ['parent_id' => 'id']); }
    public function getProducts(): ActiveQuery { return $this->hasMany(Product::class, ['category_id' => 'id']); }

    public static function getDropdownList(): array
    {
        $cats = static::find()->where(['is_active' => 1])->orderBy(['sort_order' => SORT_ASC])->all();
        $map = [];
        foreach ($cats as $c) { $map[$c->id] = $c->name; }
        return $map;
    }
}
