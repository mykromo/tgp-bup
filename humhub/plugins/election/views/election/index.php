<?php

use humhub\libs\Html;
use humhub\modules\election\models\Election;
use humhub\modules\content\components\ContentContainerActiveRecord;

/* @var $elections Election[] */
/* @var $canCreate bool */
/* @var $contentContainer ContentContainerActiveRecord */

$this->pageTitle = Yii::t('ElectionModule.base', 'Officer Elections');
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <strong><?= Yii::t('ElectionModule.base', 'Officer Elections') ?></strong>
        <?php if ($canCreate): ?>
            <div class="pull-right">
                <a href="<?= $contentContainer->createUrl('/election/position/index') ?>"
                   class="btn btn-default btn-sm">
                    <i class="fa fa-cog"></i> <?= Yii::t('ElectionModule.base', 'Manage Positions') ?>
                </a>
                <a href="<?= $contentContainer->createUrl('/election/election/create') ?>"
                   class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> <?= Yii::t('ElectionModule.base', 'Create Election') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <div class="panel-body">
        <?php if (empty($elections)): ?>
            <div class="text-center text-muted">
                <p><i class="fa fa-check-square-o" style="font-size:48px"></i></p>
                <p><?= Yii::t('ElectionModule.base', 'No elections have been created yet.') ?></p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?= Yii::t('ElectionModule.base', 'Title') ?></th>
                            <th><?= Yii::t('ElectionModule.base', 'Status') ?></th>
                            <th><?= Yii::t('ElectionModule.base', 'Created') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($elections as $election): ?>
                            <tr>
                                <td><?= Html::encode($election->title) ?></td>
                                <td>
                                    <span class="label label-<?= $election->isOpen() ? 'success' : 'default' ?>">
                                        <?php if ($election->isExpired()): ?>
                                            <?= Yii::t('ElectionModule.base', 'Expired') ?>
                                        <?php elseif (!$election->isOpen()): ?>
                                            <?= Yii::t('ElectionModule.base', 'Closed') ?>
                                        <?php else: ?>
                                            <?= Yii::t('ElectionModule.base', 'Open') ?>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td><?= Yii::$app->formatter->asDatetime($election->created_at) ?></td>
                                <td>
                                    <a href="<?= $contentContainer->createUrl('/election/election/view', ['id' => $election->id]) ?>"
                                       class="btn btn-default btn-sm">
                                        <?= Yii::t('ElectionModule.base', 'View') ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
