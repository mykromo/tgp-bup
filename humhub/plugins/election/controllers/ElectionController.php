<?php

namespace humhub\modules\election\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\election\models\Election;
use humhub\modules\election\models\ElectionCandidate;
use humhub\modules\election\models\ElectionPosition;
use humhub\modules\election\models\ElectionVote;
use humhub\modules\election\permissions\CreateElection;
use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class ElectionController extends ContentContainerController
{
    public $validContentContainerClasses = [Space::class];

    public function actionIndex()
    {
        $elections = Election::find()
            ->contentContainer($this->contentContainer)
            ->orderBy(['election.created_at' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'elections' => $elections,
            'canCreate' => $this->contentContainer->permissionManager->can(CreateElection::class),
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionCreate()
    {
        if (!$this->contentContainer->permissionManager->can(CreateElection::class)) {
            throw new ForbiddenHttpException();
        }

        $election = new Election($this->contentContainer);

        if ($election->load(Yii::$app->request->post()) && $election->validate()) {
            $election->save();
            return $this->redirect($this->contentContainer->createUrl('/election/election/view', ['id' => $election->id]));
        }

        return $this->render('create', [
            'election' => $election,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionView($id)
    {
        $election = $this->findElection($id);
        $userId = Yii::$app->user->id;
        $results = $election->getResults();
        $isMember = $this->contentContainer->isMember();

        return $this->render('view', [
            'election' => $election,
            'results' => $results,
            'userId' => $userId,
            'isMember' => $isMember,
            'contentContainer' => $this->contentContainer,
            'canManage' => $this->contentContainer->permissionManager->can(CreateElection::class),
        ]);
    }

    /**
     * File candidacy — any chapter member can file for any open position.
     * The form is auto-filled with the member's profile data.
     */
    public function actionFileCandidacy($electionId)
    {
        $election = $this->findElection($electionId);

        if (!$election->isOpen()) {
            throw new HttpException(403, Yii::t('ElectionModule.base', 'This election is closed or has expired.'));
        }

        if (!$this->contentContainer->isMember()) {
            throw new ForbiddenHttpException(Yii::t('ElectionModule.base', 'You must be a chapter member to file candidacy.'));
        }

        $user = Yii::$app->user->getIdentity();
        $positions = ElectionPosition::getPositionMap($this->contentContainer->id);

        $candidate = new ElectionCandidate();
        $candidate->election_id = $election->id;
        $candidate->user_id = $user->id;

        if ($candidate->load(Yii::$app->request->post()) && $candidate->validate()) {
            // Check if already filed for this position
            if ($election->hasFiled($user->id, $candidate->position)) {
                $candidate->addError('position', Yii::t('ElectionModule.base', 'You have already filed candidacy for this position.'));
            } else {
                $candidate->created_by = $user->id;
                if ($candidate->save()) {
                    $this->view->saved();
                    return $this->redirect($this->contentContainer->createUrl('/election/election/view', ['id' => $election->id]));
                }
            }
        }

        return $this->render('file-candidacy', [
            'election' => $election,
            'candidate' => $candidate,
            'user' => $user,
            'positions' => $positions,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionVote()
    {
        $this->forcePostRequest();

        $electionId = (int) Yii::$app->request->post('electionId');
        $candidateId = (int) Yii::$app->request->post('candidateId');
        $position = Yii::$app->request->post('position');

        $election = $this->findElection($electionId);

        if (!$election->isOpen()) {
            throw new HttpException(403, Yii::t('ElectionModule.base', 'This election is closed or has expired.'));
        }

        if (!$this->contentContainer->isMember()) {
            throw new ForbiddenHttpException();
        }

        $candidate = ElectionCandidate::findOne(['id' => $candidateId, 'election_id' => $electionId, 'position' => $position]);
        if (!$candidate) {
            throw new NotFoundHttpException();
        }

        $userId = Yii::$app->user->id;

        if ($election->hasVoted($userId, $position)) {
            $this->view->error(Yii::t('ElectionModule.base', 'You have already voted for this position.'));
            return $this->redirect($this->contentContainer->createUrl('/election/election/view', ['id' => $electionId]));
        }

        $vote = new ElectionVote();
        $vote->election_id = $electionId;
        $vote->candidate_id = $candidateId;
        $vote->user_id = $userId;
        $vote->position = $position;

        if ($vote->save()) {
            $this->view->saved();
        }

        return $this->redirect($this->contentContainer->createUrl('/election/election/view', ['id' => $electionId]));
    }

    public function actionClose($id)
    {
        if (!$this->contentContainer->permissionManager->can(CreateElection::class)) {
            throw new ForbiddenHttpException();
        }

        $election = $this->findElection($id);
        $election->status = Election::STATUS_CLOSED;
        $election->save(false);

        return $this->redirect($this->contentContainer->createUrl('/election/election/view', ['id' => $id]));
    }

    public function actionReopen($id)
    {
        if (!$this->contentContainer->permissionManager->can(CreateElection::class)) {
            throw new ForbiddenHttpException();
        }

        $election = $this->findElection($id);
        $election->status = Election::STATUS_OPEN;
        $election->save(false);

        return $this->redirect($this->contentContainer->createUrl('/election/election/view', ['id' => $id]));
    }

    private function findElection(int $id): Election
    {
        $election = Election::find()
            ->contentContainer($this->contentContainer)
            ->where(['election.id' => $id])
            ->one();

        if (!$election) {
            throw new NotFoundHttpException();
        }

        return $election;
    }
}
