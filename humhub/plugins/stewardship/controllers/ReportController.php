<?php

namespace humhub\modules\stewardship\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\stewardship\models\Fund;
use humhub\modules\stewardship\models\FunctionalCategory;
use humhub\modules\stewardship\models\Grant;
use humhub\modules\stewardship\models\Transaction;
use humhub\modules\stewardship\models\AuditLog;
use humhub\modules\space\models\Space;
use Yii;

class ReportController extends ContentContainerController
{
    public $validContentContainerClasses = [Space::class];

    /**
     * Statement of Functional Expenses — visible to all chapter members
     */
    public function actionFunctionalExpenses()
    {
        $spaceId = $this->contentContainer->id;
        $categories = FunctionalCategory::getActiveMap($spaceId);

        $rows = Transaction::find()
            ->select(['functional_category', 'program_name', 'SUM(amount) as total'])
            ->where(['space_id' => $spaceId, 'type' => 'expense', 'is_voided' => 0])
            ->groupBy(['functional_category', 'program_name'])
            ->asArray()->all();

        return $this->render('functional-expenses', [
            'rows' => $rows,
            'categories' => $categories,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    /**
     * Grant utilization report — visible to all chapter members
     */
    public function actionGrantUtilization()
    {
        $grants = Grant::find()->where(['space_id' => $this->contentContainer->id])->all();

        return $this->render('grant-utilization', [
            'grants' => $grants,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    /**
     * Audit trail — visible to all chapter members (read-only)
     */
    public function actionAuditTrail()
    {
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
     * Fund balance summary — visible to all chapter members
     */
    public function actionFundSummary()
    {
        $funds = Fund::find()->where(['space_id' => $this->contentContainer->id])->orderBy(['fund_type' => SORT_ASC])->all();

        return $this->render('fund-summary', [
            'funds' => $funds,
            'contentContainer' => $this->contentContainer,
        ]);
    }
}
