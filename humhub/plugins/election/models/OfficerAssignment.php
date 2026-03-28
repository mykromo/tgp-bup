<?php

namespace humhub\modules\election\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use Yii;
use yii\db\ActiveQuery;

/**
 * @property int $id
 * @property int $space_id
 * @property int $position_id
 * @property int $user_id
 * @property int $election_id
 * @property string $created_at
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property ElectionPosition $position
 * @property User $user
 */
class OfficerAssignment extends ActiveRecord
{
    public static function tableName()
    {
        return 'election_officer';
    }

    public function rules()
    {
        return [
            [['space_id', 'position_id', 'user_id'], 'required'],
            [['space_id', 'position_id', 'user_id', 'election_id', 'updated_by'], 'integer'],
        ];
    }

    public function getPosition(): ActiveQuery
    {
        return $this->hasOne(ElectionPosition::class, ['id' => 'position_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Get all officer assignments for a space, keyed by position_id.
     */
    public static function getForSpace(int $spaceId): array
    {
        return static::find()
            ->where(['space_id' => $spaceId])
            ->indexBy('position_id')
            ->all();
    }

    /**
     * Assign or update an officer for a position.
     */
    public static function assign(int $spaceId, int $positionId, int $userId, ?int $electionId = null): self
    {
        $record = static::findOne(['space_id' => $spaceId, 'position_id' => $positionId]);
        if (!$record) {
            $record = new static();
            $record->space_id = $spaceId;
            $record->position_id = $positionId;
        }
        $record->user_id = $userId;
        $record->election_id = $electionId;
        $record->updated_by = Yii::$app->user->id ?? null;
        $record->save(false);
        return $record;
    }

    /**
     * Populate officer assignments from election winners.
     */
    public static function populateFromElection(Election $election): void
    {
        $spaceId = $election->content->container->id;
        $winners = $election->getWinners();

        foreach ($winners as $w) {
            // Find position ID by title
            $position = ElectionPosition::find()
                ->where(['space_id' => $spaceId, 'title' => $w['position']])
                ->one();
            if ($position) {
                static::assign($spaceId, $position->id, $w['user']->id, $election->id);
            }
        }
    }
}
