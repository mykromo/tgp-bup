<?php

namespace humhub\modules\election\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use yii\db\ActiveQuery;

/**
 * @property int $id
 * @property int $election_id
 * @property int $candidate_id
 * @property int $user_id
 * @property string $position
 * @property string $created_at
 *
 * @property Election $election
 * @property ElectionCandidate $candidate
 * @property User $user
 */
class ElectionVote extends ActiveRecord
{
    public static function tableName()
    {
        return 'election_vote';
    }

    public function rules()
    {
        return [
            [['election_id', 'candidate_id', 'user_id', 'position'], 'required'],
            [['election_id', 'candidate_id', 'user_id'], 'integer'],
            [['position'], 'string', 'max' => 100],
            [['election_id', 'user_id', 'position'], 'unique',
                'targetAttribute' => ['election_id', 'user_id', 'position'],
                'message' => 'You have already voted for this position.',
            ],
        ];
    }

    public function getElection(): ActiveQuery
    {
        return $this->hasOne(Election::class, ['id' => 'election_id']);
    }

    public function getCandidate(): ActiveQuery
    {
        return $this->hasOne(ElectionCandidate::class, ['id' => 'candidate_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
