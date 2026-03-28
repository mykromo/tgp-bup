<?php

namespace humhub\modules\election\models;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\election\permissions\CreateElection;
use humhub\modules\election\widgets\WallEntry;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\Url;

/**
 * Election lifecycle:
 *   1. CANDIDACY  — members file candidacy (until candidacy_expires_at)
 *   2. VOTING     — candidacy closed, members vote (until voting_expires_at)
 *   3. COMPLETED  — voting closed, winners determined
 *   Admin can also manually close at any time (status = STATUS_CLOSED).
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $status
 * @property string $expires_at (legacy, unused)
 * @property string $candidacy_expires_at
 * @property string $voting_expires_at
 * @property int $results_posted
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 */
class Election extends ContentActiveRecord
{
    const STATUS_OPEN = 0;
    const STATUS_CLOSED = 1;

    const PHASE_CANDIDACY = 'candidacy';
    const PHASE_VOTING = 'voting';
    const PHASE_COMPLETED = 'completed';
    const PHASE_CLOSED = 'closed';

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
            [['title', 'candidacy_expires_at', 'voting_expires_at'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['status'], 'integer'],
            [['candidacy_expires_at', 'voting_expires_at', 'expires_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => Yii::t('ElectionModule.base', 'Title'),
            'description' => Yii::t('ElectionModule.base', 'Description'),
            'candidacy_expires_at' => Yii::t('ElectionModule.base', 'Filing of Candidacy Deadline'),
            'voting_expires_at' => Yii::t('ElectionModule.base', 'Voting Deadline'),
        ];
    }

    public function beforeSave($insert)
    {
        foreach (['candidacy_expires_at', 'voting_expires_at', 'expires_at'] as $attr) {
            if ($this->$attr) {
                $ts = strtotime($this->$attr);
                if ($ts !== false) {
                    $this->$attr = date('Y-m-d H:i:s', $ts);
                }
            }
        }
        return parent::beforeSave($insert);
    }

    // ── Phase logic ──

    public function getPhase(): string
    {
        if ($this->status === self::STATUS_CLOSED) {
            return self::PHASE_CLOSED;
        }
        $now = time();
        if ($now < strtotime($this->candidacy_expires_at)) {
            return self::PHASE_CANDIDACY;
        }
        if ($now < strtotime($this->voting_expires_at)) {
            return self::PHASE_VOTING;
        }
        return self::PHASE_COMPLETED;
    }

    public function isCandidacyOpen(): bool
    {
        return $this->getPhase() === self::PHASE_CANDIDACY;
    }

    public function isVotingOpen(): bool
    {
        return $this->getPhase() === self::PHASE_VOTING;
    }

    public function isCompleted(): bool
    {
        return in_array($this->getPhase(), [self::PHASE_COMPLETED, self::PHASE_CLOSED]);
    }

    public function isOpen(): bool
    {
        return in_array($this->getPhase(), [self::PHASE_CANDIDACY, self::PHASE_VOTING]);
    }

    public function getPhaseLabel(): string
    {
        return match ($this->getPhase()) {
            self::PHASE_CANDIDACY => Yii::t('ElectionModule.base', 'Filing of Candidacy'),
            self::PHASE_VOTING => Yii::t('ElectionModule.base', 'Voting'),
            self::PHASE_COMPLETED => Yii::t('ElectionModule.base', 'Completed'),
            self::PHASE_CLOSED => Yii::t('ElectionModule.base', 'Closed'),
        };
    }

    public function getPhaseBadgeClass(): string
    {
        return match ($this->getPhase()) {
            self::PHASE_CANDIDACY => 'label-info',
            self::PHASE_VOTING => 'label-success',
            self::PHASE_COMPLETED => 'label-default',
            self::PHASE_CLOSED => 'label-danger',
        };
    }

    // ── Relations ──

    public function getCandidates(): ActiveQuery
    {
        return $this->hasMany(ElectionCandidate::class, ['election_id' => 'id']);
    }

    public function getVotes(): ActiveQuery
    {
        return $this->hasMany(ElectionVote::class, ['election_id' => 'id']);
    }

    // ── Helpers ──

    public function hasVoted(int $userId, string $position): bool
    {
        return ElectionVote::find()
            ->where(['election_id' => $this->id, 'user_id' => $userId, 'position' => $position])
            ->exists();
    }

    public function hasFiled(int $userId, string $position): bool
    {
        return ElectionCandidate::find()
            ->where(['election_id' => $this->id, 'user_id' => $userId, 'position' => $position])
            ->exists();
    }

    public function getVoteCount(int $candidateId): int
    {
        return (int) ElectionVote::find()->where(['candidate_id' => $candidateId])->count();
    }

    public function getCandidatesForPosition(string $position): array
    {
        return ElectionCandidate::find()
            ->where(['election_id' => $this->id, 'position' => $position])
            ->all();
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
     * Returns the winners (top vote-getter per position).
     */
    public function getWinners(): array
    {
        $results = $this->getResults();
        $winners = [];
        foreach ($results as $positionKey => $data) {
            if (!empty($data['candidates']) && $data['candidates'][0]['votes'] > 0) {
                $winners[] = [
                    'position' => $data['label'],
                    'user' => $data['candidates'][0]['user'],
                    'votes' => $data['candidates'][0]['votes'],
                ];
            }
        }
        return $winners;
    }

    // ── Auto-post results ──

    /**
     * Checks if the election just completed and posts results to the space wall.
     * Safe to call multiple times — only posts once.
     */
    public function checkAndPostResults(): void
    {
        if ($this->results_posted) {
            return;
        }

        if ($this->getPhase() !== self::PHASE_COMPLETED) {
            return;
        }

        $this->postResultsToWall();
        OfficerAssignment::populateFromElection($this);
        $this->updateAttributes(['results_posted' => 1]);
    }

    /**
     * Creates a Post on the space wall announcing the elected officers.
     */
    private function postResultsToWall(): void
    {
        $winners = $this->getWinners();
        if (empty($winners)) {
            return;
        }

        $lines = [];
        $lines[] = '🏆 **' . Yii::t('ElectionModule.base', 'Election Results: {title}', ['title' => $this->title]) . '**';
        $lines[] = '';
        $lines[] = Yii::t('ElectionModule.base', 'The following officers have been elected:');
        $lines[] = '';

        foreach ($winners as $w) {
            $lines[] = '⭐ **' . $w['position'] . '** — ' . $w['user']->displayName . ' (' . $w['votes'] . ' ' . Yii::t('ElectionModule.base', 'votes') . ')';
        }

        $lines[] = '';
        $lines[] = Yii::t('ElectionModule.base', 'Congratulations to all newly elected officers!');

        $message = implode("\n", $lines);

        $post = new \humhub\modules\post\models\Post($this->content->container);
        $post->message = $message;
        $post->save();
    }

    // ── Content interface ──

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
}
