<?php
use humhub\libs\Html;
use humhub\modules\stewardship\models\Fund;
$this->pageTitle = Yii::t('StewardshipModule.base', 'Fund Balance Summary');
$cc = $contentContainer;
$typeLabels = Fund::getTypeLabels();
$byType = [];
foreach ($funds as $f) { $byType[$f->fund_type][] = $f; }
?>
<div class="panel panel-default">
    <div class="panel-heading"><strong><i class="fa fa-balance-scale"></i> <?= $this->pageTitle ?></strong></div>
    <div class="panel-body">
        <?php foreach ($typeLabels as $type => $label): ?>
            <h4><?= $label ?></h4>
            <?php if (empty($byType[$type])): ?>
                <p class="text-muted"><?= Yii::t('StewardshipModule.base', 'No funds in this category.') ?></p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr>
                        <th><?= Yii::t('StewardshipModule.base', 'Fund Name') ?></th>
                        <th><?= Yii::t('StewardshipModule.base', 'Purpose/Restriction') ?></th>
                        <th class="text-right"><?= Yii::t('StewardshipModule.base', 'Balance') ?></th>
                        <th><?= Yii::t('StewardshipModule.base', 'Status') ?></th>
                    </tr></thead>
                    <tbody>
                    <?php $subtotal = 0; foreach ($byType[$type] as $f): $subtotal += $f->balance; ?>
                    <tr>
                        <td><?= Html::encode($f->name) ?></td>
                        <td class="text-muted"><?= Html::encode($f->restriction_purpose ?: '—') ?></td>
                        <td class="text-right"><?= Yii::$app->formatter->asCurrency($f->balance) ?></td>
                        <td><?= $f->is_active ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Inactive</span>' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="active"><td colspan="2" class="text-right"><strong>Subtotal</strong></td>
                        <td class="text-right"><strong><?= Yii::$app->formatter->asCurrency($subtotal) ?></strong></td><td></td></tr>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <a href="<?= $cc->createUrl('/stewardship/dashboard/index') ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?= Yii::t('StewardshipModule.base', 'Back') ?></a>
    </div>
</div>
