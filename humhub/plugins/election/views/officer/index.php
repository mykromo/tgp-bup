<?php

use humhub\libs\Html;
use humhub\modules\election\models\Election;
use humhub\modules\content\components\ContentContainerActiveRecord;

/* @var $election Election|null */
/* @var $winners array */
/* @var $contentContainer ContentContainerActiveRecord */

$this->pageTitle = Yii::t('ElectionModule.base', 'Elected Officers');
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <strong><i class="fa fa-id-badge"></i> <?= Yii::t('ElectionModule.base', 'Elected Officers') ?></strong>
        <?php if ($election): ?>
            <span class="text-muted pull-right">
                <?= Yii::t('ElectionModule.base', 'From: {title}', ['title' => Html::encode($election->title)]) ?>
            </span>
        <?php endif; ?>
    </div>
    <div class="panel-body">
        <?php if (empty($winners)): ?>
            <div class="text-center text-muted" style="padding:30px 0">
                <p><i class="fa fa-id-badge" style="font-size:48px"></i></p>
                <p><?= Yii::t('ElectionModule.base', 'No elected officers yet. Officers will appear here after an election is completed.') ?></p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($winners as $winner): ?>
                    <div class="col-sm-6 col-md-4" style="margin-bottom:20px">
                        <div class="panel panel-default" style="text-align:center; padding:15px">
                            <img src="<?= $winner['user']->getProfileImage()->getUrl() ?>"
                                 class="img-circle" width="80" height="80"
                                 alt="<?= Html::encode($winner['user']->displayName) ?>"
                                 style="margin-bottom:10px">
                            <h4 style="margin:5px 0"><?= Html::encode($winner['user']->displayName) ?></h4>
                            <?php if ($winner['user']->profile->title): ?>
                                <p class="text-muted" style="margin:0"><?= Html::encode($winner['user']->profile->title) ?></p>
                            <?php endif; ?>
                            <hr style="margin:10px 0">
                            <span class="label label-primary" style="font-size:13px">
                                <i class="fa fa-star"></i> <?= Html::encode($winner['position']) ?>
                            </span>
                            <p class="text-muted" style="margin-top:8px; font-size:12px">
                                <?= Yii::t('ElectionModule.base', '{count} votes', ['count' => $winner['votes']]) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
