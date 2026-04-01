<?php

namespace humhub\modules\stewardship\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\stewardship\models\Fund;
use humhub\modules\stewardship\models\Grant;
use humhub\modules\stewardship\models\Transaction;
use humhub\modules\stewardship\permissions\ManageFinances;
use humhub\modules\space\models\Space;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class TransactionController extends ContentContainerController
{
    public $validContentContainerClasses = [Space::class];

    public function actionCreate()
    {
        if (!$this->contentContainer->permissionManager->can(ManageFinances::class)) {
            throw new ForbiddenHttpException();
        }

        $model = new Transaction();
        $model->space_id = $this->contentContainer->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
            return $this->redirect($this->contentContainer->createUrl('/stewardship/dashboard/index'));
        }

        $spaceId = $this->contentContainer->id;
        return $this->render('form', [
            'model' => $model,
            'funds' => Fund::find()->where(['space_id' => $spaceId, 'is_active' => 1])->all(),
            'grants' => Grant::find()->where(['space_id' => $spaceId, 'status' => 'active'])->all(),
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionVoid($id)
    {
        if (!$this->contentContainer->permissionManager->can(ManageFinances::class)) {
            throw new ForbiddenHttpException();
        }
        $this->forcePostRequest();

        $model = Transaction::findOne(['id' => $id, 'space_id' => $this->contentContainer->id]);
        if (!$model) throw new NotFoundHttpException();

        $reason = Yii::$app->request->post('reason', 'Voided by admin');
        $model->void($reason);

        $this->view->saved();
        return $this->redirect($this->contentContainer->createUrl('/stewardship/dashboard/index'));
    }

    public function actionLedger()
    {
        if (!$this->contentContainer->permissionManager->can(\humhub\modules\stewardship\permissions\ViewFinances::class)) {
            throw new ForbiddenHttpException();
        }

        $spaceId = $this->contentContainer->id;
        $fundId = Yii::$app->request->get('fund_id');
        $query = Transaction::find()->where(['space_id' => $spaceId])->orderBy(['transaction_date' => SORT_DESC, 'id' => SORT_DESC]);
        if ($fundId) {
            $query->andWhere(['fund_id' => $fundId]);
        }

        return $this->render('ledger', [
            'transactions' => $query->all(),
            'funds' => Fund::find()->where(['space_id' => $spaceId])->all(),
            'selectedFund' => $fundId,
            'contentContainer' => $this->contentContainer,
        ]);
    }
}
