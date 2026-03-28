<?php

namespace humhub\modules\election\models;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\election\permissions\CreateElection;
use humhub\modules\election\widgets\WallEntry;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\Url;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $status
 * @property string $expires_at
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property ElectionCandidate[] $candidates
 * @property ElectionVote[] $votes
 */
class Election extends ContentActiveRecord
{
    const STATUS_OPEN = 0;
    const STATUS_CLOSED = 1;

    /**
     * @deprecated Use ElectionPosition::getForSpace() for dynamic positions
     */
    const POSITIONS = [
        'grand_triskelion' => 'Grand Triskelion',
        'deputy_grand_triskelion' => 'Deputy Grand Triskelion',
        'master_wielder_of_the_whip' => 'Master Wielder of the Whip',
        'master_keeper_of_the_scroll' => 'Master Keeper of the Scroll',
        'master_keeper_of_the_chest' => 'Master Keeper of the Chest',
    ];

    public $wallEntryClass = WallEntry::class;
    public $moduleId = 'election';
    protected $createPermission = CreateElection::class;

    public static function tableName()
    {
        return 'election';
    }

    public function rules()
    {
        return [
            [['title', 'expires_at'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['status'], 'integer'],
            [['expires_at'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => Yii::t('ElectionModule.base', 'Title'),
            'description' => Yii::t('ElectionModule.base', 'Description'),
            'status' => Yii::t('ElectionModule.base', 'Status'),
            'expires_at' => Yii::t('ElectionModule.base', 'Expiration Date'),
        ];
    }

    public function getCandidates(): ActiveQuery
    {
        return $this->hasMany(ElectionCandidate::class, ['election_id' => 'id']);
    }

    public function getVotes(): ActiveQuery
    {
        return $this->hasMany(ElectionVote::class, ['election_id' => 'id']);
    }

    public function isOpen(): bool
    {
        if ($this->status === self::STATUS_CLOSED) {
            return false;
        }

        if ($this->expires_at && strtotime($this->expires_at) <= time()) {
            return false;
        }

        return true;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && strtotime($this->expires_at) <= time();
    }

    public function hasVoted(int $userId, string $position): bool
    {
        return ElectionVote::find()
            ->where(['election_id' => $this->id, 'user_id' => $userId, 'position' => $position])
            ->exists();
    }

    public function getVoteCount(int $candidateId): int
    {
        return (int) ElectionVote::find()
            ->where(['candidate_id' => $candidateId])
            ->count();
    }

    public function getCandidatesForPosition(string $position): array
    {
        return ElectionCandidate::find()
            ->where(['election_id' => $this->id, 'position' => $position])
            ->all();
    }

    public function getContentName()
    {
        return Yii::t('ElectionModule.base', 'Officer Election');
    }

    public function getContentDescription()
    {
        return $this->title;
    }

    public function getIcon()
    {
        return 'fa-check-square-o';
    }

    public function getUrl()
    {
        return Url::to(['/election/election/view', 'id' => $this->id, 'contentContainer' => $this->content->container]);
    }

    public function getResults(): array
    {
        $spaceId = $this->content->container->id;
        $positions = ElectionPosition::getForSpace($spaceId);
        $results = [];

        foreach ($positions as $position) {
            $key = (string) $position->id;
            $candidates = $this->getCandidatesForPosition($key);
            $positionResults = [];
            foreach ($candidates as $candidate) {
                $positionResults[] = [
                    'candidate' => $candidate,
                    'user' => $candidate->user,
                    'votes' => $this->getVoteCount($candidate->id),
                ];
            }
            usort($positionResults, fn($a, $b) => $b['votes'] - $a['votes']);
            $results[$key] = [
                'label' => $position->title,
                'candidates' => $positionResults,
            ];
        }
        return $results;
    }

    /**
     * Check if a user has already filed candidacy for a position in this election.
     */
    public function hasFiled(int $userId, string $position): bool
    {
        return ElectionCandidate::find()
            ->where(['election_id' => $this->id, 'user_id' => $userId, 'position' => $position])
            ->exists();
    }
}
