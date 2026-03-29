<?php

namespace humhub\modules\election\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\election\models\Election;
use humhub\modules\election\models\ElectionPosition;
use humhub\modules\election\models\OfficerAssignment;
use humhub\modules\election\permissions\CreateElection;
use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class OfficerController extends ContentContainerController
{
    public $validContentContainerClasses = [Space::class];

    public function actionIndex()
    {
        // Auto-post any completed elections
        $elections = Election::find()
            ->contentContainer($this->contentContainer)
            ->orderBy(['election.created_at' => SORT_DESC])
            ->all();

        foreach ($elections as $e) {
            $e->checkAndPostResults();
        }

        $spaceId = $this->contentContainer->id;
        $positions = ElectionPosition::getForSpace($spaceId);
        $assignments = OfficerAssignment::getForSpace($spaceId);
        $canManage = $this->contentContainer->permissionManager->can(CreateElection::class);

        return $this->render('index', [
            'positions' => $positions,
            'assignments' => $assignments,
            'canManage' => $canManage,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionChange($positionId)
    {
        if (!$this->contentContainer->permissionManager->can(CreateElection::class)) {
            throw new ForbiddenHttpException();
        }

        $spaceId = $this->contentContainer->id;
        $position = ElectionPosition::findOne(['id' => $positionId, 'space_id' => $spaceId]);
        if (!$position) {
            throw new NotFoundHttpException();
        }

        $members = $this->getSpaceMembers();
        $current = OfficerAssignment::findOne(['space_id' => $spaceId, 'position_id' => $positionId]);

        if (Yii::$app->request->isPost) {
            $userId = (int) Yii::$app->request->post('user_id');
            if ($userId > 0) {
                OfficerAssignment::assign($spaceId, $positionId, $userId);
                $this->view->saved();
            }
            return $this->redirect($this->contentContainer->createUrl('/election/officer/index'));
        }

        return $this->render('change', [
            'position' => $position,
            'members' => $members,
            'current' => $current,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionVacate($positionId)
    {
        if (!$this->contentContainer->permissionManager->can(CreateElection::class)) {
            throw new ForbiddenHttpException();
        }
        $this->forcePostRequest();

        $spaceId = $this->contentContainer->id;
        $record = OfficerAssignment::findOne(['space_id' => $spaceId, 'position_id' => $positionId]);
        if ($record) {
            $record->delete();
        }

        $this->view->saved();
        return $this->redirect($this->contentContainer->createUrl('/election/officer/index'));
    }

    public function actionTogglePosition($positionId)
    {
        if (!$this->contentContainer->permissionManager->can(CreateElection::class)) {
            throw new ForbiddenHttpException();
        }
        $this->forcePostRequest();

        $model = ElectionPosition::findOne(['id' => $positionId, 'space_id' => $this->contentContainer->id]);
        if (!$model) {
            throw new NotFoundHttpException();
        }

        $model->is_active = $model->isActive() ? 0 : 1;
        $model->save(false);

        $this->view->saved();
        return $this->redirect($this->contentContainer->createUrl('/election/officer/index'));
    }

    public function actionDeletePosition($positionId)
    {
        if (!$this->contentContainer->permissionManager->can(CreateElection::class)) {
            throw new ForbiddenHttpException();
        }
        $this->forcePostRequest();

        $model = ElectionPosition::findOne(['id' => $positionId, 'space_id' => $this->contentContainer->id]);
        if (!$model) {
            throw new NotFoundHttpException();
        }

        if ($model->isDefault()) {
            throw new \yii\web\HttpException(403, Yii::t('ElectionModule.base', 'Default positions cannot be deleted.'));
        }

        // Remove officer assignment if exists
        $assignment = OfficerAssignment::findOne(['space_id' => $this->contentContainer->id, 'position_id' => $positionId]);
        if ($assignment) {
            $assignment->delete();
        }

        $model->delete();
        $this->view->saved();
        return $this->redirect($this->contentContainer->createUrl('/election/officer/index'));
    }

    private function getSpaceMembers(): array
    {
        return User::find()
            ->innerJoin('space_membership', 'space_membership.user_id = user.id')
            ->innerJoin('space', 'space.id = space_membership.space_id')
            ->where(['space.id' => $this->contentContainer->id])
            ->andWhere(['space_membership.status' => Membership::STATUS_MEMBER])
            ->orderBy(['user.username' => SORT_ASC])
            ->all();
    }
}
