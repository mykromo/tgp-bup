<?php

use humhub\libs\Html;
use humhub\modules\election\models\ElectionPosition;
use humhub\modules\election\models\OfficerAssignment;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\user\models\User;

/* @var $position ElectionPosition */
/* @var $members User[] */
/* @var $current OfficerAssignment|null */
/* @var $contentContainer ContentContainerActiveRecord */

$this->pageTitle = Yii::t('ElectionModule.base', 'Change Officer');
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <strong>
            <i class="fa fa-exchange"></i>
            <?= Yii::t('ElectionModule.base', 'Change Officer for: {position}', ['position' => Html::encode($position->title)]) ?>
        </strong>
    </div>
    <div class="panel-body">

        <?php if ($current && $current->user): ?>
            <div class="well">
                <strong><?= Yii::t('ElectionModule.base', 'Current Officer:') ?></strong>
                <div class="media" style="margin-top:8px">
                    <div class="media-left">
                        <img src="<?= $current->user->getProfileImage()->getUrl() ?>"
                             class="img-rounded" width="48" height="48">
                    </div>
                    <div class="media-body">
                        <h5 class="media-heading"><?= Html::encode($current->user->displayName) ?></h5>
                        <?php if ($current->user->profile->title): ?>
                            <span class="text-muted"><?= Html::encode($current->user->profile->title) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?= Html::beginForm($contentContainer->createUrl('/election/officer/change', ['positionId' => $position->id]), 'post') ?>

        <div class="form-group">
            <label class="control-label"><?= Yii::t('ElectionModule.base', 'Select New Officer') ?></label>
            <select name="user_id" class="form-control" required>
                <option value=""><?= Yii::t('ElectionModule.base', '— Select Member —') ?></option>
                <?php foreach ($members as $member): ?>
                    <option value="<?= $member->id ?>"
                        <?= ($current && $current->user_id == $member->id) ? 'selected' : '' ?>>
                        <?= Html::encode($member->displayName) ?>
                        <?php if ($member->profile->title): ?>
                            (<?= Html::encode($member->profile->title) ?>)
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <hr>
        <div class="form-group">
            <?= Html::submitButton(
                '<i class="fa fa-check"></i> ' . Yii::t('ElectionModule.base', 'Save'),
                ['class' => 'btn btn-primary']
            ) ?>
            <a href="<?= $contentContainer->createUrl('/election/officer/index') ?>" class="btn btn-default">
                <?= Yii::t('ElectionModule.base', 'Cancel') ?>
            </a>
        </div>

        <?= Html::endForm() ?>
    </div>
</div>
