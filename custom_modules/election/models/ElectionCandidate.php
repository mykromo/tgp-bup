<?php

namespace humhub\modules\election\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use Yii;
use yii\db\ActiveQuery;

/**
 * @property int $id
 * @property int $election_id
 * @property string $position
 * @property int $user_id
 * @property string $statement
 * @property string $created_at
 * @property int $created_by
 *
 * @property Election $election
 * @property User $user
 * @property ElectionVote[] $votes
 * @property ElectionPosition $positionModel
 */
class ElectionCandidate extends ActiveRecord
{
    public static function tableName()
    {
        return 'election_candidate';
    }

    public function rules()
    {
        return [
            [['election_id', 'position', 'user_id'], 'required'],
            [['election_id', 'user_id'], 'integer'],
            [['position'], 'string', 'max' => 100],
            [['statement'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'position' => Yii::t('ElectionModule.base', 'Position'),
            'statement' => Yii::t('ElectionModule.base', 'Platform / Statement'),
        ];
    }

    public function getElection(): ActiveQuery
    {
        return $this->hasOne(Election::class, ['id' => 'election_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getVotes(): ActiveQuery
    {
        return $this->hasMany(ElectionVote::class, ['candidate_id' => 'id']);
    }

    public function getPositionModel(): ActiveQuery
    {
        return $this->hasOne(ElectionPosition::class, ['id' => 'position']);
    }
}
