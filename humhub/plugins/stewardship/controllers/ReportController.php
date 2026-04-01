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
use yii\data\Pagination;

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
        $query = Grant::find()->where(['space_id' => $this->contentContainer->id]);
        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);

        return $this->render('grant-utilization', [
            'grants' => $query->offset($pagination->offset)->limit($pagination->limit)->all(),
            'pagination' => $pagination,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    /**
     * Audit trail — visible to all chapter members (read-only)
     */
    public function actionAuditTrail()
    {
        $query = AuditLog::find()
            ->where(['space_id' => $this->contentContainer->id])
            ->orderBy(['created_at' => SORT_DESC]);
        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => 25]);

        return $this->render('audit-trail', [
            'logs' => $query->offset($pagination->offset)->limit($pagination->limit)->all(),
            'pagination' => $pagination,
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

    public function actionExportFunctionalExpenses()
    {
        $spaceId = $this->contentContainer->id;
        $format = Yii::$app->request->get('format', 'csv');
        $categories = FunctionalCategory::getActiveMap($spaceId);
        $rows = Transaction::find()
            ->select(['functional_category', 'program_name', 'SUM(amount) as total'])
            ->where(['space_id' => $spaceId, 'type' => 'expense', 'is_voided' => 0])
            ->groupBy(['functional_category', 'program_name'])
            ->asArray()->all();

        $headers = ['Category', 'Program', 'Total'];
        $data = [];
        foreach ($rows as $r) {
            $cat = $r['functional_category'] ?: 'unclassified';
            $data[] = [$categories[$cat] ?? ucfirst($cat), $r['program_name'] ?: 'General', number_format((float) $r['total'], 2)];
        }

        \humhub\modules\stewardship\helpers\Export::download($format, 'functional-expenses', 'Statement of Functional Expenses', $headers, $data);
    }

    public function actionExportGrantUtilization()
    {
        $format = Yii::$app->request->get('format', 'csv');
        $grants = Grant::find()->where(['space_id' => $this->contentContainer->id])->all();

        $headers = ['Grant', 'Grantor', 'Fund', 'Awarded', 'Spent', 'Remaining', 'Utilization %', 'Status'];
        $data = [];
        foreach ($grants as $g) {
            $data[] = [$g->name, $g->grantor ?? '', $g->fund->name ?? '', number_format((float) $g->amount_awarded, 2), number_format((float) $g->amount_spent, 2), number_format($g->getAmountRemaining(), 2), $g->getUtilizationPercent() . '%', ucfirst($g->status)];
        }

        \humhub\modules\stewardship\helpers\Export::download($format, 'grant-utilization', 'Grant Utilization Report', $headers, $data);
    }

    public function actionExportAuditTrail()
    {
        $format = Yii::$app->request->get('format', 'csv');
        $logs = AuditLog::find()->where(['space_id' => $this->contentContainer->id])->orderBy(['created_at' => SORT_DESC])->all();

        $headers = ['Date', 'User', 'Action', 'Entity', 'Field', 'Old Value', 'New Value'];
        $data = [];
        foreach ($logs as $log) {
            $data[] = [$log->created_at, $log->user->displayName ?? 'System', $log->action, $log->entity_type . ' #' . $log->entity_id, $log->field_changed ?? '', $log->old_value ?? '', $log->new_value ?? ''];
        }

        \humhub\modules\stewardship\helpers\Export::download($format, 'audit-trail', 'Audit Trail', $headers, $data);
    }

    public function actionExportFundSummary()
    {
        $format = Yii::$app->request->get('format', 'csv');
        $funds = Fund::find()->where(['space_id' => $this->contentContainer->id])->orderBy(['fund_type' => SORT_ASC])->all();

        $headers = ['Fund Name', 'Type', 'Purpose/Restriction', 'Balance', 'Status'];
        $data = [];
        foreach ($funds as $f) {
            $data[] = [$f->name, ucfirst(str_replace('_', ' ', $f->fund_type)), $f->restriction_purpose ?? '', number_format((float) $f->balance, 2), $f->is_active ? 'Active' : 'Inactive'];
        }

        \humhub\modules\stewardship\helpers\Export::download($format, 'fund-summary', 'Fund Balance Summary', $headers, $data);
    }
}
