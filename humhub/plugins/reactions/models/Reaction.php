<?php

namespace humhub\modules\reactions\models;

use humhub\components\behaviors\PolymorphicRelation;
use humhub\modules\content\components\ContentAddonActiveRecord;
use humhub\modules\reactions\Module;
use humhub\modules\user\models\User;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $object_model
 * @property string $object_id
 * @property string $reaction_type
 * @property int $created_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class Reaction extends ActiveRecord
{
    public static function tableName()
    {
        return 'reaction';
    }

    public function behaviors()
    {
        return [
            [
                'class' => PolymorphicRelation::class,
                'mustBeInstanceOf' => [ActiveRecord::class],
            ],
        ];
    }

    public function rules()
    {
        return [
            [['object_model', 'object_id', 'reaction_type'], 'required'],
            [['object_id', 'created_by'], 'integer'],
            [['object_model'], 'string', 'max' => 100],
            [['reaction_type'], 'string', 'max' => 20],
            [['reaction_type'], 'in', 'range' => array_keys(Module::REACTIONS)],
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert && !$this->created_by) {
            $this->created_by = Yii::$app->user->id;
        }
        if ($insert && !$this->created_at) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        $this->updated_at = date('Y-m-d H:i:s');
        return parent::beforeSave($insert);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Get all reactions for an object, grouped by type with counts.
     */
    public static function getSummary(string $objectModel, int $objectId): array
    {
        $rows = static::find()
            ->select(['reaction_type', 'COUNT(*) as cnt'])
            ->where(['object_model' => $objectModel, 'object_id' => $objectId])
            ->groupBy('reaction_type')
            ->asArray()
            ->all();

        $summary = [];
        foreach ($rows as $row) {
            $summary[$row['reaction_type']] = (int) $row['cnt'];
        }
        return $summary;
    }

    /**
     * Get the current user's reaction for an object (if any).
     */
    public static function getUserReaction(string $objectModel, int $objectId, int $userId): ?self
    {
        return static::findOne([
            'object_model' => $objectModel,
            'object_id' => $objectId,
            'created_by' => $userId,
        ]);
    }

    /**
     * Get users who reacted with a specific type.
     */
    public static function getUsersByType(string $objectModel, int $objectId, string $type): array
    {
        return User::find()
            ->innerJoin('reaction', 'reaction.created_by = user.id')
            ->where([
                'reaction.object_model' => $objectModel,
                'reaction.object_id' => $objectId,
                'reaction.reaction_type' => $type,
            ])
            ->all();
    }
}
