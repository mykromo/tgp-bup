<?php

use humhub\libs\Html;
use humhub\modules\election\models\ElectionPosition;
use humhub\modules\content\components\ContentContainerActiveRecord;

/* @var $positions ElectionPosition[] */
/* @var $contentContainer ContentContainerActiveRecord */

$this->pageTitle = Yii::t('ElectionModule.base', 'Manage Positions');
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <strong><i class="fa fa-cog"></i> <?= Yii::t('ElectionModule.base', 'Manage Officer Positions') ?></strong>
        <a href="<?= $contentContainer->createUrl('/election/position/create') ?>"
           class="btn btn-primary btn-sm pull-right">
            <i class="fa fa-plus"></i> <?= Yii::t('ElectionModule.base', 'Add Position') ?>
        </a>
    </div>
    <div class="panel-body">
        <p class="help-block">
            <?= Yii::t('ElectionModule.base', 'Define the officer positions available for elections in this chapter.') ?>
        </p>

        <?php if (empty($positions)): ?>
            <p class="text-muted text-center"><?= Yii::t('ElectionModule.base', 'No positions defined yet.') ?></p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?= Yii::t('ElectionModule.base', 'Position Title') ?></th>
                            <th><?= Yii::t('ElectionModule.base', 'Sort Order') ?></th>
                            <th class="text-right"><?= Yii::t('ElectionModule.base', 'Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($positions as $i => $pos): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= Html::encode($pos->title) ?></td>
                                <td><?= $pos->sort_order ?></td>
                                <td class="text-right">
                                    <a href="<?= $contentContainer->createUrl('/election/position/update', ['id' => $pos->id]) ?>"
                                       class="btn btn-default btn-xs">
                                        <i class="fa fa-pencil"></i> <?= Yii::t('ElectionModule.base', 'Edit') ?>
                                    </a>
                                    <a href="<?= $contentContainer->createUrl('/election/position/delete', ['id' => $pos->id]) ?>"
                                       class="btn btn-danger btn-xs"
                                       data-method="post"
                                       data-confirm="<?= Yii::t('ElectionModule.base', 'Are you sure you want to delete this position?') ?>">
                                        <i class="fa fa-trash"></i> <?= Yii::t('ElectionModule.base', 'Delete') ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <hr>
        <a href="<?= $contentContainer->createUrl('/election/election/index') ?>" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> <?= Yii::t('ElectionModule.base', 'Back to Elections') ?>
        </a>
    </div>
</div>
