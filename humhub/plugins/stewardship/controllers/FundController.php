<?php

namespace humhub\modules\stewardship\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\stewardship\models\AuditLog;
use humhub\modules\stewardship\models\Fund;
use humhub\modules\stewardship\permissions\ManageFinances;
use humhub\modules\space\models\Space;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class FundController extends ContentContainerController
{
    public $validContentContainerClasses = [Space::class];

    private function requireManage()
    {
        if (!$this->contentContainer->permissionManager->can(ManageFinances::class)) {
            throw new ForbiddenHttpException();
        }
    }

    public function actionCreate()
    {
        $this->requireManage();
        $model = new Fund();
        $model->space_id = $this->contentContainer->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            AuditLog::log($model->space_id, 'fund', $model->id, 'created');
            $this->view->saved();
            return $this->redirect($this->contentContainer->createUrl('/stewardship/dashboard/index'));
        }

        return $this->render('form', [
            'model' => $model,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionUpdate($id)
    {
        $this->requireManage();
        $model = Fund::findOne(['id' => $id, 'space_id' => $this->contentContainer->id]);
        if (!$model) throw new NotFoundHttpException();

        $oldType = $model->fund_type;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($oldType !== $model->fund_type) {
                AuditLog::log($model->space_id, 'fund', $model->id, 'updated', 'fund_type', $oldType, $model->fund_type);
            }
            $this->view->saved();
            return $this->redirect($this->contentContainer->createUrl('/stewardship/dashboard/index'));
        }

        return $this->render('form', [
            'model' => $model,
            'contentContainer' => $this->contentContainer,
        ]);
    }
}
