<?php

use humhub\libs\Html;
use humhub\modules\election\models\Election;
use humhub\modules\election\models\ElectionCandidate;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\user\models\User;
use yii\bootstrap\ActiveForm;

/* @var $election Election */
/* @var $candidate ElectionCandidate */
/* @var $user User */
/* @var $positions array id => title */
/* @var $contentContainer ContentContainerActiveRecord */

$this->pageTitle = Yii::t('ElectionModule.base', 'File Candidacy');
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <strong><?= Yii::t('ElectionModule.base', 'File Candidacy') ?></strong>
        — <?= Html::encode($election->title) ?>
    </div>
    <div class="panel-body">

        <div class="well">
            <div class="media">
                <div class="media-left">
                    <img src="<?= $user->getProfileImage()->getUrl() ?>"
                         class="img-rounded" width="64" height="64"
                         alt="<?= Html::encode($user->displayName) ?>">
                </div>
                <div class="media-body">
                    <h4 class="media-heading"><?= Html::encode($user->displayName) ?></h4>
                    <p class="text-muted">
                        <?php if ($user->profile->title): ?>
                            <?= Html::encode($user->profile->title) ?><br>
                        <?php endif; ?>
                        <?= Html::encode($user->email) ?>
                    </p>
                </div>
            </div>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'candidacy-form']); ?>

        <?= $form->field($candidate, 'position')->dropDownList($positions, [
            'prompt' => Yii::t('ElectionModule.base', '— Select Position —'),
        ]) ?>

        <?= $form->field($candidate, 'statement')->textarea([
            'rows' => 5,
            'placeholder' => Yii::t('ElectionModule.base', 'Why are you running for this position? Share your platform or statement here...'),
        ]) ?>

        <?= Html::hiddenInput('ElectionCandidate[election_id]', $election->id) ?>
        <?= Html::hiddenInput('ElectionCandidate[user_id]', $user->id) ?>

        <hr>
        <div class="form-group">
            <?= Html::submitButton(
                '<i class="fa fa-check"></i> ' . Yii::t('ElectionModule.base', 'Submit Candidacy'),
                ['class' => 'btn btn-primary']
            ) ?>
            <a href="<?= $contentContainer->createUrl('/election/election/view', ['id' => $election->id]) ?>"
               class="btn btn-default">
                <?= Yii::t('ElectionModule.base', 'Cancel') ?>
            </a>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
