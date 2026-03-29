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
 * @property string $candidacy_start_at
 * @property string $candidacy_expires_at
 * @property string $voting_start_at
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
    const STATUS_CANCELLED = 2;

    const PHASE_CANDIDACY = 'candidacy';
    const PHASE_VOTING = 'voting';
    const PHASE_COMPLETED = 'completed';
    const PHASE_CLOSED = 'closed';
    const PHASE_CANCELLED = 'cancelled';

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
            [['title', 'candidacy_start_at', 'candidacy_expires_at', 'voting_start_at', 'voting_expires_at'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['status'], 'integer'],
            [['candidacy_start_at', 'candidacy_expires_at', 'voting_start_at', 'voting_expires_at', 'expires_at'], 'safe'],
            [['candidacy_expires_at'], 'validateCandidacyRange'],
            [['voting_start_at'], 'validateVotingAfterCandidacy'],
            [['voting_expires_at'], 'validateVotingRange'],
        ];
    }

    /**
     * Candidacy end must be after candidacy start.
     */
    public function validateCandidacyRange($attribute)
    {
        if ($this->candidacy_start_at && $this->candidacy_expires_at) {
            if (strtotime($this->candidacy_expires_at) <= strtotime($this->candidacy_start_at)) {
                $this->addError($attribute, Yii::t('ElectionModule.base', 'Candidacy end must be after candidacy start.'));
            }
        }
    }

    /**
     * Voting must start at or after candidacy ends (same date allowed if time doesn't overlap).
     */
    public function validateVotingAfterCandidacy($attribute)
    {
        if ($this->candidacy_expires_at && $this->voting_start_at) {
            $candidacyEnd = strtotime($this->candidacy_expires_at);
            $votingStart = strtotime($this->voting_start_at);
            if ($votingStart < $candidacyEnd) {
                $this->addError($attribute, Yii::t('ElectionModule.base', 'Voting start must be at or after candidacy end. They can be on the same date but the times must not overlap.'));
            }
        }
    }

    /**
     * Voting end must be after voting start.
     */
    public function validateVotingRange($attribute)
    {
        if ($this->voting_start_at && $this->voting_expires_at) {
            if (strtotime($this->voting_expires_at) <= strtotime($this->voting_start_at)) {
                $this->addError($attribute, Yii::t('ElectionModule.base', 'Voting end must be after voting start.'));
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'title' => Yii::t('ElectionModule.base', 'Title'),
            'description' => Yii::t('ElectionModule.base', 'Description'),
            'candidacy_start_at' => Yii::t('ElectionModule.base', 'Candidacy Start Date'),
            'candidacy_expires_at' => Yii::t('ElectionModule.base', 'Candidacy End Date'),
            'voting_start_at' => Yii::t('ElectionModule.base', 'Voting Start Date'),
            'voting_expires_at' => Yii::t('ElectionModule.base', 'Voting End Date'),
        ];
    }

    public function beforeSave($insert)
    {
        // Normalize all date inputs to MySQL datetime format (preserve time)
        foreach (['candidacy_start_at', 'candidacy_expires_at', 'voting_start_at', 'voting_expires_at', 'expires_at'] as $attr) {
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
        if ($this->status === self::STATUS_CANCELLED) {
            return self::PHASE_CANCELLED;
        }
        if ($this->status === self::STATUS_CLOSED) {
            return self::PHASE_CLOSED;
        }
        $now = time();
        $candidacyStart = strtotime($this->candidacy_start_at);
        $candidacyEnd = strtotime($this->candidacy_expires_at);
        $votingStart = strtotime($this->voting_start_at);
        $votingEnd = strtotime($this->voting_expires_at);

        if ($now < $candidacyStart) {
            return self::PHASE_CANDIDACY; // upcoming, treat as candidacy
        }
        if ($now >= $candidacyStart && $now < $candidacyEnd) {
            return self::PHASE_CANDIDACY;
        }
        if ($now >= $votingStart && $now < $votingEnd) {
            return self::PHASE_VOTING;
        }
        if ($now >= $votingEnd) {
            return self::PHASE_COMPLETED;
        }
        // Between candidacy end and voting start (gap)
        return self::PHASE_VOTING;
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

    public function isCancelled(): bool
    {
        return $this->getPhase() === self::PHASE_CANCELLED;
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
            self::PHASE_CANCELLED => Yii::t('ElectionModule.base', 'Cancelled'),
        };
    }

    public function getPhaseBadgeClass(): string
    {
        return match ($this->getPhase()) {
            self::PHASE_CANDIDACY => 'label-info',
            self::PHASE_VOTING => 'label-success',
            self::PHASE_COMPLETED => 'label-default',
            self::PHASE_CLOSED => 'label-danger',
            self::PHASE_CANCELLED => 'label-warning',
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
        $positions = ElectionPosition::getActiveForSpace($spaceId);
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

    // ── Calendar integration ──

    /**
     * Creates two calendar events: one for the candidacy period, one for voting.
     * Stores the calendar entry IDs so they can be deleted on cancel.
     */
    public function createCalendarEvents(): void
    {
        if (!class_exists('humhub\modules\calendar\models\CalendarEntry')) {
            return;
        }

        $container = $this->content->container;

        if (!$container->isModuleEnabled('calendar')) {
            return;
        }

        $dbFormat = 'Y-m-d H:i:s';

        try {
            // Candidacy period: candidacy_start_at → candidacy_expires_at
            $candidacy = new \humhub\modules\calendar\models\CalendarEntry($container);
            $candidacy->title = Yii::t('ElectionModule.base', 'Filing of Candidacy: {title}', ['title' => $this->title]);
            $candidacy->description = Yii::t('ElectionModule.base', 'Chapter members can file their candidacy for officer positions during this period.');
            $candidacy->start_datetime = date('Y-m-d 00:00:00', strtotime($this->candidacy_start_at));
            $candidacy->end_datetime = date('Y-m-d 23:59:00', strtotime($this->candidacy_expires_at));
            $candidacy->all_day = 1;
            $candidacy->color = '#5bc0de';
            $candidacy->content->visibility = \humhub\modules\content\models\Content::VISIBILITY_PUBLIC;
            if ($candidacy->save()) {
                $this->updateAttributes(['candidacy_calendar_id' => $candidacy->id]);
            } else {
                Yii::error('Election candidacy calendar event failed: ' . json_encode($candidacy->getErrors()), 'election');
            }

            // Voting period: voting_start_at → voting_expires_at
            $voting = new \humhub\modules\calendar\models\CalendarEntry($container);
            $voting->title = Yii::t('ElectionModule.base', 'Voting: {title}', ['title' => $this->title]);
            $voting->description = Yii::t('ElectionModule.base', 'Chapter members can cast their votes for officer positions during this period.');
            $voting->start_datetime = date('Y-m-d 00:00:00', strtotime($this->voting_start_at));
            $voting->end_datetime = date('Y-m-d 23:59:00', strtotime($this->voting_expires_at));
            $voting->all_day = 1;
            $voting->color = '#5cb85c';
            $voting->content->visibility = \humhub\modules\content\models\Content::VISIBILITY_PUBLIC;
            if ($voting->save()) {
                $this->updateAttributes(['voting_calendar_id' => $voting->id]);
            } else {
                Yii::error('Election voting calendar event failed: ' . json_encode($voting->getErrors()), 'election');
            }
        } catch (\Throwable $e) {
            Yii::error('Election calendar event creation failed: ' . $e->getMessage(), 'election');
        }
    }

    /**
     * Deletes the associated calendar events (used when election is cancelled).
     */
    public function deleteCalendarEvents(): void
    {
        if (!class_exists('humhub\modules\calendar\models\CalendarEntry')) {
            return;
        }

        try {
            foreach (['candidacy_calendar_id', 'voting_calendar_id'] as $attr) {
                if ($this->$attr) {
                    $entry = \humhub\modules\calendar\models\CalendarEntry::findOne($this->$attr);
                    if ($entry) {
                        $entry->hardDelete();
                    }
                }
            }
            $this->updateAttributes([
                'candidacy_calendar_id' => null,
                'voting_calendar_id' => null,
            ]);
        } catch (\Throwable $e) {
            Yii::error('Election calendar event deletion failed: ' . $e->getMessage(), 'election');
        }
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
        $this->createCompletionActivity();

        $this->updateAttributes(['results_posted' => 1]);
    }

    /**
     * Creates an Activity record directly so it appears in "Latest Activities".
     */
    private function createCompletionActivity(): void
    {
        try {
            $container = $this->content->container;
            $createdBy = Yii::$app->user->isGuest ? $this->created_by : Yii::$app->user->id;

            $activity = new \humhub\modules\activity\models\Activity($container, [
                'visibility' => \humhub\modules\content\models\Content::VISIBILITY_PUBLIC,
            ]);
            $activity->class = \humhub\modules\election\activities\ElectionCompleted::class;
            $activity->module = 'election';
            $activity->setPolymorphicRelation($this);
            $activity->content->created_by = $createdBy;
            $activity->content->visibility = \humhub\modules\content\models\Content::VISIBILITY_PUBLIC;

            if (!$activity->save()) {
                Yii::error('Election activity save failed: ' . json_encode($activity->getErrors()), 'election');
            }
        } catch (\Throwable $e) {
            Yii::error('Election activity creation failed: ' . $e->getMessage(), 'election');
        }
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
