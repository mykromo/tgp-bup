<?php
use humhub\libs\Html;
$this->title = Yii::t('StewardshipModule.base', 'Manage Functional Categories');
$cc = $contentContainer;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <strong><i class="fa fa-tags"></i> <?= $this->title ?></strong>
        <a href="<?= $cc->createUrl('/stewardship/category/create') ?>" class="btn btn-primary btn-sm pull-right">
            <i class="fa fa-plus"></i> <?= Yii::t('StewardshipModule.base', 'Add Category') ?></a>
    </div>
    <div class="panel-body">
        <p class="help-block"><?= Yii::t('StewardshipModule.base', 'Functional categories are used to classify expenses (e.g. Program Services, Management, Fundraising). Default categories cannot be deleted but can be disabled.') ?></p>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr>
                    <th><?= Yii::t('StewardshipModule.base', 'Key') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Display Name') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Order') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Status') ?></th>
                    <th><?= Yii::t('StewardshipModule.base', 'Type') ?></th>
                    <th class="text-right"><?= Yii::t('StewardshipModule.base', 'Actions') ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr class="<?= !$cat->is_active ? 'text-muted' : '' ?>">
                    <td><code><?= Html::encode($cat->key) ?></code></td>
                    <td><?= Html::encode($cat->label) ?></td>
                    <td><?= $cat->sort_order ?></td>
                    <td><span class="label label-<?= $cat->is_active ? 'success' : 'default' ?>"><?= $cat->is_active ? 'Active' : 'Disabled' ?></span></td>
                    <td><span class="label label-<?= $cat->isDefault() ? 'info' : 'warning' ?>"><?= $cat->isDefault() ? 'Default' : 'Custom' ?></span></td>
                    <td class="text-right" style="white-space:nowrap">
                        <a href="<?= $cc->createUrl('/stewardship/category/update', ['id' => $cat->id]) ?>" class="btn btn-default btn-xs">
                            <i class="fa fa-pencil"></i> <?= Yii::t('StewardshipModule.base', 'Edit') ?></a>
                        <a href="<?= $cc->createUrl('/stewardship/category/toggle', ['id' => $cat->id]) ?>"
                           class="btn btn-<?= $cat->is_active ? 'warning' : 'success' ?> btn-xs" data-method="post">
                            <?= $cat->is_active ? '<i class="fa fa-eye-slash"></i> Disable' : '<i class="fa fa-eye"></i> Enable' ?></a>
                        <?php if (!$cat->isDefault()): ?>
                        <a href="<?= $cc->createUrl('/stewardship/category/delete', ['id' => $cat->id]) ?>"
                           class="btn btn-danger btn-xs" data-method="post"
                           data-confirm="<?= Yii::t('StewardshipModule.base', 'Delete this category?') ?>">
                            <i class="fa fa-trash"></i> <?= Yii::t('StewardshipModule.base', 'Delete') ?></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="<?= $cc->createUrl('/stewardship/dashboard/index') ?>" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> <?= Yii::t('StewardshipModule.base', 'Back') ?></a>
    </div>
</div>
