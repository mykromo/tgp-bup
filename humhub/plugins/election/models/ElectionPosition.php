<?php

namespace humhub\modules\election\models;

use humhub\components\ActiveRecord;
use Yii;

/**
 * @property int $id
 * @property int $space_id
 * @property string $title
 * @property int $sort_order
 * @property int $is_default
 * @property int $is_active
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 */
class ElectionPosition extends ActiveRecord
{
    public static function tableName()
    {
        return 'election_position';
    }

    public function rules()
    {
        return [
            [['space_id', 'title'], 'required'],
            [['space_id', 'sort_order'], 'integer'],
            [['sort_order'], 'default', 'value' => 0],
            [['title'], 'string', 'max' => 255],
            [['is_default', 'is_active'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => Yii::t('ElectionModule.base', 'Position Title'),
            'sort_order' => Yii::t('ElectionModule.base', 'Sort Order'),
            'is_active' => Yii::t('ElectionModule.base', 'Active'),
        ];
    }

    /**
     * Returns ALL positions for a space (including disabled), creating defaults if none exist.
     */
    public static function getForSpace(int $spaceId): array
    {
        $positions = static::find()
            ->where(['space_id' => $spaceId])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();

        if (empty($positions)) {
            $positions = static::seedDefaults($spaceId);
        }

        return $positions;
    }

    /**
     * Returns only ACTIVE positions for a space.
     */
    public static function getActiveForSpace(int $spaceId): array
    {
        $all = static::getForSpace($spaceId);
        return array_filter($all, fn($p) => (int) $p->is_active === 1);
    }

    public static function seedDefaults(int $spaceId): array
    {
        $defaults = [
            ['Grand Triskelion', 1],
            ['Deputy Grand Triskelion', 2],
            ['Master Wielder of the Whip', 3],
            ['Master Keeper of the Scroll', 4],
            ['Master Keeper of the Chest', 5],
        ];

        $result = [];
        foreach ($defaults as [$title, $order]) {
            $pos = new static();
            $pos->space_id = $spaceId;
            $pos->title = $title;
            $pos->sort_order = $order;
            $pos->is_default = 1;
            $pos->is_active = 1;
            $pos->save(false);
            $result[] = $pos;
        }

        return $result;
    }

    /**
     * Returns active positions as id => title map (for candidacy/voting dropdowns).
     */
    public static function getPositionMap(int $spaceId): array
    {
        $positions = static::getActiveForSpace($spaceId);
        $map = [];
        foreach ($positions as $pos) {
            $map[$pos->id] = $pos->title;
        }
        return $map;
    }

    public function isDefault(): bool
    {
        return (int) $this->is_default === 1;
    }

    public function isActive(): bool
    {
        return (int) $this->is_active === 1;
    }
}
