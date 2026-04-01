<?php
use humhub\libs\Html;
use humhub\modules\stewardship\models\Fund;
use humhub\modules\stewardship\models\Transaction;
/* @var $funds Fund[] */
/* @var $grants \humhub\modules\stewardship\models\Grant[] */
/* @var $recentTxns Transaction[] */
/* @var $totalByType array */
/* @var $canManage bool */
$this->title = Yii::t('StewardshipModule.base', 'Financial Dashboard');
$cc = $contentContainer;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <strong><i class="fa fa-book"></i> <?= Yii::t('StewardshipModule.base', 'Financial Dashboard') ?></strong>
        <?php if ($canManage): ?>
        <div class="pull-right">
            <a href="<?= $cc->createUrl('/stewardship/fund/create') ?>" class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i> <?= Yii::t('StewardshipModule.base', 'New Fund') ?></a>
            <a href="<?= $cc->createUrl('/stewardship/transaction/create') ?>" class="btn btn-success btn-sm">
                <i class="fa fa-plus"></i> <?= Yii::t('StewardshipModule.base', 'New Transaction') ?></a>
        </div>
        <?php endif; ?>
    </div>
    <div class="panel-body">
        <!-- Fund Balances by Type -->
        <div class="row">
            <?php foreach (Fund::getTypeLabels() as $type => $label): ?>
            <div class="col-sm-4">
                <div class="panel panel-default text-center" style="padding:15px">
                    <h5><?= $label ?></h5>
                    <h3><?= \humhub\modules\stewardship\helpers\Currency::format($totalByType[$type] ?? 0) ?></h3>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Quick Links -->
        <div style="margin:15px 0">
            <a href="<?= $cc->createUrl('/stewardship/transaction/ledger') ?>" class="btn btn-default btn-sm">
                <i class="fa fa-list"></i> <?= Yii::t('StewardshipModule.base', 'Full Ledger') ?></a>
            <a href="<?= $cc->createUrl('/stewardship/report/functional-expenses') ?>" class="btn btn-default btn-sm">
                <i class="fa fa-pie-chart"></i> <?= Yii::t('StewardshipModule.base', 'Functional Expenses') ?></a>
            <a href="<?= $cc->createUrl('/stewardship/report/grant-utilization') ?>" class="btn btn-default btn-sm">
                <i class="fa fa-bar-chart"></i> <?= Yii::t('StewardshipModule.base', 'Grant Utilization') ?></a>
            <a href="<?= $cc->createUrl('/stewardship/report/fund-summary') ?>" class="btn btn-default btn-sm">
                <i class="fa fa-balance-scale"></i> <?= Yii::t('StewardshipModule.base', 'Fund Summary') ?></a>
            <a href="<?= $cc->createUrl('/stewardship/report/audit-trail') ?>" class="btn btn-default btn-sm">
                <i class="fa fa-shield"></i> <?= Yii::t('StewardshipModule.base', 'Audit Trail') ?></a>
            <?php if ($canManage): ?>
            <a href="<?= $cc->createUrl('/stewardship/category/index') ?>" class="btn btn-default btn-sm">
                <i class="fa fa-tags"></i> <?= Yii::t('StewardshipModule.base', 'Manage Categories') ?></a>
            <?php endif; ?>
        </div>

        <!-- Active Grants -->
        <?php if (!empty($grants)): ?>
        <h4><?= Yii::t('StewardshipModule.base', 'Active Grants') ?></h4>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr>
                    <th><?= Yii::t('StewardshipModule.base', 'Grant') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Awarded') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Spent') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Remaining') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Utilization') ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($grants as $g): ?>
                    <tr>
                        <td><?= Html::encode($g->name) ?></td>
                        <td><?= \humhub\modules\stewardship\helpers\Currency::format($g->amount_awarded) ?></td>
                        <td><?= \humhub\modules\stewardship\helpers\Currency::format($g->amount_spent) ?></td>
                        <td><?= \humhub\modules\stewardship\helpers\Currency::format($g->getAmountRemaining()) ?></td>
                        <td>
                            <div class="progress" style="margin:0;min-width:80px">
                                <div class="progress-bar <?= $g->getUtilizationPercent() > 90 ? 'progress-bar-danger' : 'progress-bar-info' ?>"
                                     style="width:<?= $g->getUtilizationPercent() ?>%">
                                    <?= $g->getUtilizationPercent() ?>%
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Recent Transactions -->
        <h4><?= Yii::t('StewardshipModule.base', 'Recent Transactions') ?></h4>
        <?php if (empty($recentTxns)): ?>
            <p class="text-muted"><?= Yii::t('StewardshipModule.base', 'No transactions recorded yet.') ?></p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr>
                    <th><?= Yii::t('StewardshipModule.base', 'Date') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Fund') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Type') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Description') ?></th>
                    <th class="text-right"><?= Yii::t('StewardshipModule.base', 'Amount') ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($recentTxns as $t): ?>
                    <tr class="<?= $t->is_voided ? 'text-muted' : '' ?>">
                        <td><?= Yii::$app->formatter->asDate($t->transaction_date) ?></td>
                        <td><?= Html::encode($t->fund->name ?? '—') ?></td>
                        <td><span class="label label-<?= in_array($t->type, ['income','transfer_in']) ? 'success' : 'warning' ?>">
                            <?= Transaction::getTypeLabels()[$t->type] ?? $t->type ?></span></td>
                        <td><?= Html::encode($t->description) ?><?= $t->is_voided ? ' <span class="label label-danger">VOID</span>' : '' ?></td>
                        <td class="text-right"><?= \humhub\modules\stewardship\helpers\Currency::format($t->amount) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
