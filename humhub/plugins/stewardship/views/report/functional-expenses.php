<?php
use humhub\libs\Html;
use humhub\modules\stewardship\models\Transaction;
$this->pageTitle = Yii::t('StewardshipModule.base', 'Statement of Functional Expenses');
$cc = $contentContainer;
$grouped = [];
foreach ($rows as $r) {
    $cat = $r['functional_category'] ?: 'unclassified';
    $prog = $r['program_name'] ?: 'General';
    $grouped[$cat][$prog] = (float) $r['total'];
}
?>
<div class="panel panel-default">
    <div class="panel-heading"><strong><i class="fa fa-pie-chart"></i> <?= $this->pageTitle ?></strong></div>
    <div class="panel-body">
        <p class="help-block"><?= Yii::t('StewardshipModule.base', 'Expenses broken down by functional category and program, as required for non-profit transparency reporting.') ?></p>
        <?php if (empty($grouped)): ?>
            <p class="text-muted"><?= Yii::t('StewardshipModule.base', 'No expense data available.') ?></p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead><tr>
                    <th><?= Yii::t('StewardshipModule.base', 'Category') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Program') ?></th>
                    <th class="text-right"><?= Yii::t('StewardshipModule.base', 'Total') ?></th>
                </tr></thead>
                <tbody>
                <?php $grandTotal = 0; foreach ($grouped as $cat => $programs): ?>
                    <?php $catTotal = array_sum($programs); $grandTotal += $catTotal; ?>
                    <?php foreach ($programs as $prog => $total): ?>
                    <tr>
                        <td><?= $categories[$cat] ?? ucfirst($cat) ?></td>
                        <td><?= Html::encode($prog) ?></td>
                        <td class="text-right"><?= \humhub\modules\stewardship\helpers\Currency::format($total) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="active"><td colspan="2" class="text-right"><strong><?= $categories[$cat] ?? ucfirst($cat) ?> Total</strong></td>
                        <td class="text-right"><strong><?= \humhub\modules\stewardship\helpers\Currency::format($catTotal) ?></strong></td></tr>
                <?php endforeach; ?>
                <tr class="info"><td colspan="2" class="text-right"><strong>Grand Total</strong></td>
                    <td class="text-right"><strong><?= \humhub\modules\stewardship\helpers\Currency::format($grandTotal) ?></strong></td></tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <a href="<?= $cc->createUrl('/stewardship/dashboard/index') ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?= Yii::t('StewardshipModule.base', 'Back') ?></a>
    </div>
</div>
