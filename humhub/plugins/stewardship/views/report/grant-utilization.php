<?php
use humhub\libs\Html;
$this->pageTitle = Yii::t('StewardshipModule.base', 'Grant Utilization Report');
$cc = $contentContainer;
?>
<div class="panel panel-default">
    <div class="panel-heading"><strong><i class="fa fa-bar-chart"></i> <?= $this->pageTitle ?></strong></div>
    <div class="panel-body">
        <?php if (empty($grants)): ?>
            <p class="text-muted"><?= Yii::t('StewardshipModule.base', 'No grants found.') ?></p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr>
                    <th><?= Yii::t('StewardshipModule.base', 'Grant') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Grantor') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Fund') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Period') ?></th>
                    <th class="text-right"><?= Yii::t('StewardshipModule.base', 'Awarded') ?></th>
                    <th class="text-right"><?= Yii::t('StewardshipModule.base', 'Spent') ?></th>
                    <th class="text-right"><?= Yii::t('StewardshipModule.base', 'Remaining') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Utilization') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Status') ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($grants as $g): ?>
                <tr class="<?= $g->status === 'expired' ? 'warning' : '' ?>">
                    <td><?= Html::encode($g->name) ?></td>
                    <td><?= Html::encode($g->grantor) ?></td>
                    <td><?= Html::encode($g->fund->name ?? '') ?></td>
                    <td><?= $g->start_date ? Yii::$app->formatter->asDate($g->start_date) . ' — ' . Yii::$app->formatter->asDate($g->end_date) : '—' ?></td>
                    <td class="text-right"><?= \humhub\modules\stewardship\helpers\Currency::format($g->amount_awarded) ?></td>
                    <td class="text-right"><?= \humhub\modules\stewardship\helpers\Currency::format($g->amount_spent) ?></td>
                    <td class="text-right"><?= \humhub\modules\stewardship\helpers\Currency::format($g->getAmountRemaining()) ?></td>
                    <td><div class="progress" style="margin:0;min-width:60px">
                        <div class="progress-bar <?= $g->getUtilizationPercent() > 90 ? 'progress-bar-danger' : 'progress-bar-info' ?>"
                             style="width:<?= $g->getUtilizationPercent() ?>%"><?= $g->getUtilizationPercent() ?>%</div>
                    </div></td>
                    <td><span class="label label-<?= $g->status === 'active' ? 'success' : 'default' ?>"><?= ucfirst($g->status) ?></span></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <a href="<?= $cc->createUrl('/stewardship/dashboard/index') ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?= Yii::t('StewardshipModule.base', 'Back') ?></a>
    </div>
</div>
