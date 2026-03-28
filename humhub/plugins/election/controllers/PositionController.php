<?php

namespace humhub\modules\election\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\election\models\ElectionPosition;
use humhub\modules\election\permissions\CreateElection;
use humhub\modules\space\models\Space;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class PositionController extends ContentContainerController
{
    public $validContentContainerClasses = [Space::class];

    private function requireAdmin()
    {
        if (!$this->contentContainer->permissionManager->can(CreateElection::class)) {
            throw new ForbiddenHttpException();
        }
    }

    public function actionIndex()
    {
        $this->requireAdmin();
        $positions = ElectionPosition::getForSpace($this->contentContainer->id);

        return $this->render('index', [
            'positions' => $positions,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionCreate()
    {
        $this->requireAdmin();
        $model = new ElectionPosition();
        $model->space_id = $this->contentContainer->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
            return $this->redirect($this->contentContainer->createUrl('/election/position/index'));
        }

        return $this->render('form', [
            'model' => $model,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionUpdate($id)
    {
        $this->requireAdmin();
        $model = $this->findPosition($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
            return $this->redirect($this->contentContainer->createUrl('/election/position/index'));
        }

        return $this->render('form', [
            'model' => $model,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionDelete($id)
    {
        $this->requireAdmin();
        $this->forcePostRequest();

        $model = $this->findPosition($id);
        $model->delete();

        $this->view->saved();
        return $this->redirect($this->contentContainer->createUrl('/election/position/index'));
    }

    private function findPosition(int $id): ElectionPosition
    {
        $model = ElectionPosition::findOne(['id' => $id, 'space_id' => $this->contentContainer->id]);
        if (!$model) {
            throw new NotFoundHttpException();
        }
        return $model;
    }
}
