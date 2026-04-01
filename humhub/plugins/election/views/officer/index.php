<?php

use humhub\libs\Html;
use humhub\modules\election\models\ElectionPosition;
use humhub\modules\election\models\OfficerAssignment;
use humhub\modules\content\components\ContentContainerActiveRecord;

/* @var $positions ElectionPosition[] */
/* @var $assignments OfficerAssignment[] keyed by position_id */
/* @var $canManage bool */
/* @var $contentContainer ContentContainerActiveRecord */

$this->pageTitle = Yii::t('ElectionModule.base', 'Chapter Officers');
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <strong><i class="fa fa-id-badge"></i> <?= Yii::t('ElectionModule.base', 'Chapter Officers') ?></strong>
        <?php if ($canManage): ?>
            <div class="pull-right">
                <a href="<?= $contentContainer->createUrl('/election/election/index') ?>" class="btn btn-primary btn-sm">
                    <i class="fa fa-check-square-o"></i> <?= Yii::t('ElectionModule.base', 'Elections') ?></a>
                <a href="<?= $contentContainer->createUrl('/election/position/index') ?>" class="btn btn-primary btn-sm">
                    <i class="fa fa-cog"></i> <?= Yii::t('ElectionModule.base', 'Manage Positions') ?></a>
            </div>
        <?php endif; ?>
    </div>
    <div class="panel-body">
        <?php if (empty($positions)): ?>
            <div class="text-center text-muted" style="padding:30px 0">
                <p><i class="fa fa-id-badge" style="font-size:48px"></i></p>
                <p><?= Yii::t('ElectionModule.base', 'No positions defined yet.') ?></p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?= Yii::t('ElectionModule.base', 'Position') ?></th>
                            <th><?= Yii::t('ElectionModule.base', 'Officer') ?></th>
                            <?php if ($canManage): ?>
                                <th class="text-right"><?= Yii::t('ElectionModule.base', 'Actions') ?></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($positions as $pos): ?>
                            <?php
                                // Members only see active positions
                                if (!$canManage && !$pos->isActive()) {
                                    continue;
                                }
                                $assignment = $assignments[$pos->id] ?? null;
                                $user = $assignment ? $assignment->user : null;
                            ?>
                            <tr class="<?= !$pos->isActive() ? 'warning' : '' ?>">
                                <td style="vertical-align:middle">
                                    <strong><?= Html::encode($pos->title) ?></strong>
                                    <?php if ($canManage && !$pos->isActive()): ?>
                                        <span class="label label-default"><?= Yii::t('ElectionModule.base', 'Hidden') ?></span>
                                    <?php endif; ?>
                                    <?php if ($canManage && $pos->isDefault()): ?>
                                        <span class="label label-info" style="font-size:10px"><?= Yii::t('ElectionModule.base', 'Default') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="vertical-align:middle">
                                    <?php if ($user): ?>
                                        <a href="<?= $user->getUrl() ?>" style="color:inherit; text-decoration:none">
                                            <img src="<?= $user->getProfileImage()->getUrl() ?>"
                                                 class="img-circle" width="32" height="32"
                                                 alt="<?= Html::encode($user->displayName) ?>"
                                                 style="margin-right:8px">
                                            <?= Html::encode($user->displayName) ?>
                                            <?php if ($user->profile->title): ?>
                                                <small class="text-muted">— <?= Html::encode($user->profile->title) ?></small>
                                            <?php endif; ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted"><i class="fa fa-minus-circle"></i> <?= Yii::t('ElectionModule.base', 'Vacant') ?></span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($canManage): ?>
                                    <td class="text-right" style="vertical-align:middle; white-space:nowrap">
                                        <!-- Assign / Change officer -->
                                        <a href="<?= $contentContainer->createUrl('/election/officer/change', ['positionId' => $pos->id]) ?>"
                                           class="btn btn-default btn-xs">
                                            <i class="fa fa-pencil"></i>
                                            <?= $user
                                                ? Yii::t('ElectionModule.base', 'Change')
                                                : Yii::t('ElectionModule.base', 'Assign') ?>
                                        </a>

                                        <!-- Set Vacant -->
                                        <?php if ($user): ?>
                                            <a href="<?= $contentContainer->createUrl('/election/officer/vacate', ['positionId' => $pos->id]) ?>"
                                               class="btn btn-default btn-xs"
                                               data-method="post"
                                               data-confirm="<?= Yii::t('ElectionModule.base', 'Set this position as vacant?') ?>">
                                                <i class="fa fa-user-times"></i> <?= Yii::t('ElectionModule.base', 'Vacate') ?>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Hide / Show (all positions) -->
                                        <a href="<?= $contentContainer->createUrl('/election/officer/toggle-position', ['positionId' => $pos->id]) ?>"
                                           class="btn btn-<?= $pos->isActive() ? 'warning' : 'success' ?> btn-xs"
                                           data-method="post"
                                           title="<?= $pos->isActive()
                                               ? Yii::t('ElectionModule.base', 'Hide this position from members')
                                               : Yii::t('ElectionModule.base', 'Show this position to members') ?>">
                                            <?php if ($pos->isActive()): ?>
                                                <i class="fa fa-eye-slash"></i> <?= Yii::t('ElectionModule.base', 'Hide') ?>
                                            <?php else: ?>
                                                <i class="fa fa-eye"></i> <?= Yii::t('ElectionModule.base', 'Show') ?>
                                            <?php endif; ?>
                                        </a>

                                        <!-- Delete (custom positions only) -->
                                        <?php if (!$pos->isDefault()): ?>
                                            <a href="<?= $contentContainer->createUrl('/election/officer/delete-position', ['positionId' => $pos->id]) ?>"
                                               class="btn btn-danger btn-xs"
                                               data-method="post"
                                               data-confirm="<?= Yii::t('ElectionModule.base', 'Delete this position permanently?') ?>">
                                                <i class="fa fa-trash"></i> <?= Yii::t('ElectionModule.base', 'Delete') ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
