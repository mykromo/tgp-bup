<?php
use humhub\libs\Html;
$this->title = Yii::t('StewardshipModule.base', 'Audit Trail');
$cc = $contentContainer;
?>
<div class="panel panel-default">
    <div class="panel-heading"><strong><i class="fa fa-shield"></i> <?= $this->title ?></strong></div>
    <div class="panel-body">
        <p class="help-block"><?= Yii::t('StewardshipModule.base', 'Immutable log of all financial actions. Records cannot be edited or deleted.') ?></p>
        <?php if (empty($logs)): ?>
            <p class="text-muted"><?= Yii::t('StewardshipModule.base', 'No audit entries yet.') ?></p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-condensed table-hover">
                <thead><tr>
                    <th><?= Yii::t('StewardshipModule.base', 'Timestamp') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'User') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Entity') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Action') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Field') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Old Value') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'New Value') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'IP') ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td style="white-space:nowrap"><?= Yii::$app->formatter->asDatetime($log->created_at) ?></td>
                    <td><?= Html::encode($log->user->displayName ?? 'System') ?></td>
                    <td><?= Html::encode($log->entity_type) ?> #<?= $log->entity_id ?></td>
                    <td><span class="label label-<?= $log->action === 'voided' ? 'danger' : 'info' ?>"><?= $log->action ?></span></td>
                    <td><?= Html::encode($log->field_changed) ?></td>
                    <td><?= Html::encode($log->old_value) ?></td>
                    <td><?= Html::encode($log->new_value) ?></td>
                    <td><code><?= Html::encode($log->ip_address) ?></code></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <a href="<?= $cc->createUrl('/stewardship/dashboard/index') ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?= Yii::t('StewardshipModule.base', 'Back') ?></a>
    </div>
</div>
