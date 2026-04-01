<?php

namespace humhub\modules\stewardship\models;

use humhub\components\ActiveRecord;
use Yii;

/**
 * @property int $id
 * @property int $space_id
 * @property string $key
 * @property string $label
 * @property int $is_default
 * @property int $is_active
 * @property int $sort_order
 */
class FunctionalCategory extends ActiveRecord
{
    public static function tableName()
    {
        return 'stewardship_category';
    }

    public function rules()
    {
        return [
            [['space_id', 'key', 'label'], 'required'],
            [['space_id', 'sort_order'], 'integer'],
            [['key'], 'string', 'max' => 50],
            [['label'], 'string', 'max' => 255],
            [['key'], 'match', 'pattern' => '/^[a-z0-9_]+$/', 'message' => Yii::t('StewardshipModule.base', 'Key must be lowercase letters, numbers, and underscores only.')],
            [['is_default', 'is_active'], 'boolean'],
            [['sort_order'], 'default', 'value' => 0],
        ];
    }

    public function attributeLabels()
    {
        return [
            'key' => Yii::t('StewardshipModule.base', 'Key'),
            'label' => Yii::t('StewardshipModule.base', 'Display Name'),
            'sort_order' => Yii::t('StewardshipModule.base', 'Sort Order'),
            'is_active' => Yii::t('StewardshipModule.base', 'Active'),
        ];
    }

    /**
     * Get all categories for a space, seeding defaults if none exist.
     */
    public static function getForSpace(int $spaceId): array
    {
        $cats = static::find()
            ->where(['space_id' => $spaceId])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();

        if (empty($cats)) {
            $cats = static::seedDefaults($spaceId);
        }

        return $cats;
    }

    /**
     * Get only active categories as key => label map for dropdowns.
     */
    public static function getActiveMap(int $spaceId): array
    {
        $cats = static::getForSpace($spaceId);
        $map = [];
        foreach ($cats as $c) {
            if ((int) $c->is_active === 1) {
                $map[$c->key] = $c->label;
            }
        }
        return $map;
    }

    public static function seedDefaults(int $spaceId): array
    {
        $defaults = [
            ['program', 'Program Services', 1],
            ['management', 'Management & General', 2],
            ['fundraising', 'Fundraising', 3],
        ];

        $result = [];
        foreach ($defaults as [$key, $label, $order]) {
            $cat = new static();
            $cat->space_id = $spaceId;
            $cat->key = $key;
            $cat->label = $label;
            $cat->is_default = 1;
            $cat->is_active = 1;
            $cat->sort_order = $order;
            $cat->save(false);
            $result[] = $cat;
        }
        return $result;
    }

    public function isDefault(): bool
    {
        return (int) $this->is_default === 1;
    }
}
