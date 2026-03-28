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
            <div class="row">
                <?php foreach ($positions as $pos): ?>
                    <?php $assignment = $assignments[$pos->id] ?? null; ?>
                    <?php $user = $assignment ? $assignment->user : null; ?>
                    <div class="col-sm-6 col-md-4" style="margin-bottom:20px">
                        <div class="panel panel-default" style="text-align:center; padding:15px; min-height:220px">
                            <?php if ($user): ?>
                                <img src="<?= $user->getProfileImage()->getUrl() ?>"
                                     class="img-circle" width="80" height="80"
                                     alt="<?= Html::encode($user->displayName) ?>"
                                     style="margin-bottom:10px">
                                <h4 style="margin:5px 0"><?= Html::encode($user->displayName) ?></h4>
                                <?php if ($user->profile->title): ?>
                                    <p class="text-muted" style="margin:0; font-size:12px"><?= Html::encode($user->profile->title) ?></p>
                                <?php endif; ?>
                            <?php else: ?>
                                <div style="width:80px;height:80px;border-radius:50%;background:#eee;margin:0 auto 10px;line-height:80px">
                                    <i class="fa fa-user" style="font-size:36px;color:#ccc"></i>
                                </div>
                                <h4 style="margin:5px 0; color:#999"><?= Yii::t('ElectionModule.base', 'Vacant') ?></h4>
                            <?php endif; ?>

                            <hr style="margin:10px 0">
                            <span class="label label-primary" style="font-size:13px">
                                <i class="fa fa-star"></i> <?= Html::encode($pos->title) ?>
                            </span>

                            <?php if ($canManage): ?>
                                <div style="margin-top:10px">
                                    <a href="<?= $contentContainer->createUrl('/election/officer/change', ['positionId' => $pos->id]) ?>"
                                       class="btn btn-default btn-xs">
                                        <i class="fa fa-exchange"></i>
                                        <?= $user
                                            ? Yii::t('ElectionModule.base', 'Change')
                                            : Yii::t('ElectionModule.base', 'Assign') ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
