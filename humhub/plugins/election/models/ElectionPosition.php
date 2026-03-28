<?php

namespace humhub\modules\election\models;

use humhub\components\ActiveRecord;
use Yii;
use yii\db\ActiveQuery;

/**
 * @property int $id
 * @property int $space_id
 * @property string $title
 * @property int $sort_order
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
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => Yii::t('ElectionModule.base', 'Position Title'),
            'sort_order' => Yii::t('ElectionModule.base', 'Sort Order'),
        ];
    }

    /**
     * Returns all positions for a given space, creating defaults if none exist.
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
     * Seeds the default officer positions for a space.
     */
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
            $pos->save(false);
            $result[] = $pos;
        }

        return $result;
    }

    /**
     * Returns positions as id => title map for dropdowns.
     */
    public static function getPositionMap(int $spaceId): array
    {
        $positions = static::getForSpace($spaceId);
        $map = [];
        foreach ($positions as $pos) {
            $map[$pos->id] = $pos->title;
        }
        return $map;
    }
}
