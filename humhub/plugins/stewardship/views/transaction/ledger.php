<?php
use humhub\libs\Html;
use humhub\modules\stewardship\models\Transaction;
use yii\helpers\ArrayHelper;
$this->pageTitle = Yii::t('StewardshipModule.base', 'Transaction Ledger');
$cc = $contentContainer;
?>
<div class="panel panel-default">
    <div class="panel-heading"><strong><i class="fa fa-list"></i> <?= $this->pageTitle ?></strong></div>
    <div class="panel-body">
        <form method="get" action="<?= $cc->createUrl('/stewardship/transaction/ledger') ?>" class="form-inline" style="margin-bottom:15px">
            <input type="hidden" name="cguid" value="<?= $cc->guid ?>">
            <select name="fund_id" class="form-control input-sm" onchange="this.form.submit()">
                <option value=""><?= Yii::t('StewardshipModule.base', 'All Funds') ?></option>
                <?php foreach ($funds as $f): ?>
                    <option value="<?= $f->id ?>" <?= $selectedFund == $f->id ? 'selected' : '' ?>><?= Html::encode($f->name) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <?php if (empty($transactions)): ?>
            <p class="text-muted"><?= Yii::t('StewardshipModule.base', 'No transactions found.') ?></p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover table-condensed">
                <thead><tr>
                    <th><?= Yii::t('StewardshipModule.base', 'Date') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Fund') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Type') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Category') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Description') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Ref') ?></th>
                    <th class="text-right"><?= Yii::t('StewardshipModule.base', 'Amount') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Status') ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($transactions as $t): ?>
                    <tr class="<?= $t->is_voided ? 'danger' : '' ?>">
                        <td><?= Yii::$app->formatter->asDate($t->transaction_date) ?></td>
                        <td><?= Html::encode($t->fund->name ?? '') ?></td>
                        <td><?= Transaction::getTypeLabels()[$t->type] ?? $t->type ?></td>
                        <td><?= $t->functional_category ? Transaction::getFunctionalLabels()[$t->functional_category] : '' ?></td>
                        <td><?= Html::encode($t->description) ?></td>
                        <td><?= Html::encode($t->reference) ?></td>
                        <td class="text-right"><?= Yii::$app->formatter->asCurrency($t->amount) ?></td>
                        <td><?= $t->is_voided ? '<span class="label label-danger">VOID</span>' : '<span class="label label-success">Active</span>' ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <a href="<?= $cc->createUrl('/stewardship/dashboard/index') ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?= Yii::t('StewardshipModule.base', 'Back') ?></a>
    </div>
</div>
