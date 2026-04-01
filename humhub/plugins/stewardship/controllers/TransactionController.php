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
            'categories' => \humhub\modules\stewardship\models\FunctionalCategory::getActiveMap($spaceId),
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionEdit($id)
    {
        if (!$this->contentContainer->permissionManager->can(ManageFinances::class)) {
            throw new ForbiddenHttpException();
        }

        $model = Transaction::findOne(['id' => $id, 'space_id' => $this->contentContainer->id]);
        if (!$model) throw new NotFoundHttpException();

        if ($model->is_voided) {
            throw new ForbiddenHttpException(Yii::t('StewardshipModule.base', 'Voided transactions cannot be edited.'));
        }

        $oldValues = $model->getAttributes(['amount', 'description', 'fund_id', 'functional_category', 'program_name', 'reference', 'transaction_date']);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Log changes to audit trail
            foreach ($oldValues as $field => $oldVal) {
                $newVal = $model->$field;
                if ((string) $oldVal !== (string) $newVal) {
                    \humhub\modules\stewardship\models\AuditLog::log(
                        $model->space_id, 'transaction', $model->id, 'updated', $field, $oldVal, $newVal
                    );
                }
            }
            $this->view->saved();
            return $this->redirect($this->contentContainer->createUrl('/stewardship/transaction/ledger'));
        }

        $spaceId = $this->contentContainer->id;
        return $this->render('form', [
            'model' => $model,
            'funds' => Fund::find()->where(['space_id' => $spaceId, 'is_active' => 1])->all(),
            'grants' => Grant::find()->where(['space_id' => $spaceId, 'status' => 'active'])->all(),
            'categories' => \humhub\modules\stewardship\models\FunctionalCategory::getActiveMap($spaceId),
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
        // Visible to all chapter members (read-only)
        $spaceId = $this->contentContainer->id;
        $fundId = Yii::$app->request->get('fund_id');
        $query = Transaction::find()->where(['space_id' => $spaceId])->orderBy(['transaction_date' => SORT_DESC, 'id' => SORT_DESC]);
        if ($fundId) {
            $query->andWhere(['fund_id' => $fundId]);
        }

        return $this->render('ledger', [
            'transactions' => $query->all(),
            'funds' => Fund::find()->where(['space_id' => $spaceId])->all(),
            'categories' => \humhub\modules\stewardship\models\FunctionalCategory::getActiveMap($spaceId),
            'selectedFund' => $fundId,
            'contentContainer' => $this->contentContainer,
        ]);
    }
}
