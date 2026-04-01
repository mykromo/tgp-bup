<?php
use humhub\libs\Html;
$this->title = Yii::t('StewardshipModule.base', 'Audit Trail');
$cc = $contentContainer;
?>
<div class="panel panel-default">
<div class="panel-heading">
<strong><i class="fa fa-shield"></i> <?= $this->title ?></strong>
<div class="pull-right"><?= $this->render('@stewardship/views/_export_buttons', ['url' => $cc->createUrl('/stewardship/report/export-audit-trail')]) ?></div>
</div>
<div class="panel-body">
<?php if (empty($logs)): ?>
<p class="text-muted">No audit entries yet.</p>
<?php else: ?>
<div class="table-responsive">
<table class="table table-condensed table-hover">
<thead><tr>
<th>Date</th><th>By</th><th>Action</th><th>Details</th>
</tr></thead>
<tbody>
<?php foreach ($logs as $log): ?>
<tr>
<td style="white-space:nowrap"><?= Yii::$app->formatter->asDatetime($log->created_at) ?></td>
<td><?php if ($log->user): ?><a href="<?= $log->user->getUrl() ?>"><?= Html::encode($log->user->displayName) ?></a><?php else: ?>System<?php endif; ?></td>
<td><span class="label label-info"><?= ucfirst($log->action) ?></span> <?= Html::encode($log->entity_type) ?> #<?= $log->entity_id ?></td>
<td><?php if ($log->field_changed):
    $amountFields = ['amount', 'balance', 'amount_awarded', 'amount_spent'];
    $isAmount = in_array($log->field_changed, $amountFields);
    $oldVal = $isAmount && $log->old_value !== null ? \humhub\modules\stewardship\helpers\Currency::format($log->old_value) : Html::encode($log->old_value);
    $newVal = $isAmount && $log->new_value !== null ? \humhub\modules\stewardship\helpers\Currency::format($log->new_value) : Html::encode($log->new_value);
?><strong><?= Html::encode($log->field_changed) ?></strong>: <?php if ($log->old_value): ?><s class="text-muted"><?= $oldVal ?></s> &rarr; <?php endif; ?><?= $newVal ?><?php endif; ?></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
<?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
<a href="<?= $cc->createUrl('/stewardship/dashboard/index') ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
</div></div>
