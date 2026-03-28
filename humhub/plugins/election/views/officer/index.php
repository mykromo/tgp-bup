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
                            <?php $assignment = $assignments[$pos->id] ?? null; ?>
                            <?php $user = $assignment ? $assignment->user : null; ?>
                            <tr>
                                <td style="vertical-align:middle">
                                    <strong><?= Html::encode($pos->title) ?></strong>
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
                                        <a href="<?= $contentContainer->createUrl('/election/officer/change', ['positionId' => $pos->id]) ?>"
                                           class="btn btn-default btn-xs">
                                            <i class="fa fa-pencil"></i>
                                            <?= $user
                                                ? Yii::t('ElectionModule.base', 'Change')
                                                : Yii::t('ElectionModule.base', 'Assign') ?>
                                        </a>
                                        <?php if ($user): ?>
                                            <a href="<?= $contentContainer->createUrl('/election/officer/vacate', ['positionId' => $pos->id]) ?>"
                                               class="btn btn-danger btn-xs"
                                               data-method="post"
                                               data-confirm="<?= Yii::t('ElectionModule.base', 'Are you sure you want to set this position as vacant?') ?>">
                                                <i class="fa fa-times"></i> <?= Yii::t('ElectionModule.base', 'Set Vacant') ?>
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
