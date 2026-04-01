<?php

namespace humhub\modules\stewardship\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\stewardship\models\Fund;
use humhub\modules\stewardship\models\Grant;
use humhub\modules\stewardship\models\Transaction;
use humhub\modules\stewardship\models\AuditLog;
use humhub\modules\stewardship\permissions\ViewFinances;
use humhub\modules\space\models\Space;
use Yii;
use yii\web\ForbiddenHttpException;

class ReportController extends ContentContainerController
{
    public $validContentContainerClasses = [Space::class];

    /**
     * Statement of Functional Expenses
     */
    public function actionFunctionalExpenses()
    {
        if (!$this->contentContainer->permissionManager->can(ViewFinances::class)) {
            throw new ForbiddenHttpException();
        }

        $spaceId = $this->contentContainer->id;
        $rows = Transaction::find()
            ->select(['functional_category', 'program_name', 'SUM(amount) as total'])
            ->where(['space_id' => $spaceId, 'type' => 'expense', 'is_voided' => 0])
            ->groupBy(['functional_category', 'program_name'])
            ->asArray()->all();

        return $this->render('functional-expenses', [
            'rows' => $rows,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    /**
     * Grant utilization report
     */
    public function actionGrantUtilization()
    {
        if (!$this->contentContainer->permissionManager->can(ViewFinances::class)) {
            throw new ForbiddenHttpException();
        }

        $grants = Grant::find()->where(['space_id' => $this->contentContainer->id])->all();

        return $this->render('grant-utilization', [
            'grants' => $grants,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    /**
     * Audit trail
     */
    public function actionAuditTrail()
    {
        if (!$this->contentContainer->permissionManager->can(ViewFinances::class)) {
            throw new ForbiddenHttpException();
        }

        $logs = AuditLog::find()
            ->where(['space_id' => $this->contentContainer->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(100)->all();

        return $this->render('audit-trail', [
            'logs' => $logs,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    /**
     * Fund balance summary by restriction type
     */
    public function actionFundSummary()
    {
        if (!$this->contentContainer->permissionManager->can(ViewFinances::class)) {
            throw new ForbiddenHttpException();
        }

        $funds = Fund::find()->where(['space_id' => $this->contentContainer->id])->orderBy(['fund_type' => SORT_ASC])->all();

        return $this->render('fund-summary', [
            'funds' => $funds,
            'contentContainer' => $this->contentContainer,
        ]);
    }
}
