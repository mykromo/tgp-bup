<?php

use humhub\libs\Html;
use humhub\modules\election\models\Election;
use humhub\modules\content\components\ContentContainerActiveRecord;
use yii\bootstrap\ActiveForm;

/* @var $election Election */
/* @var $contentContainer ContentContainerActiveRecord */

$this->pageTitle = Yii::t('ElectionModule.base', 'Create Election');
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <strong><?= Yii::t('ElectionModule.base', 'Create Officer Election') ?></strong>
    </div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin(['id' => 'election-form']); ?>

        <?= $form->field($election, 'title')->textInput(['maxlength' => 255]) ?>
        <?= $form->field($election, 'description')->textarea(['rows' => 3]) ?>

        <div class="well">
            <h5><i class="fa fa-pencil-square-o"></i>
                <?= Yii::t('ElectionModule.base', 'Filing of Candidacy Schedule') ?>
            </h5>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($election, 'candidacy_start_at')->input('datetime-local') ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($election, 'candidacy_expires_at')->input('datetime-local') ?>
                </div>
            </div>
        </div>

        <div class="well">
            <h5><i class="fa fa-check-square-o"></i>
                <?= Yii::t('ElectionModule.base', 'Voting Schedule') ?>
            </h5>
            <p class="help-block">
                <?= Yii::t('ElectionModule.base', 'Candidacy must come before voting. They can be on the same date as long as the times do not overlap.') ?>
            </p>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($election, 'voting_start_at')->input('datetime-local') ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($election, 'voting_expires_at')->input('datetime-local') ?>
                </div>
            </div>
        </div>

        <hr>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('ElectionModule.base', 'Create Election'), ['class' => 'btn btn-primary']) ?>
            <a href="<?= $contentContainer->createUrl('/election/election/index') ?>" class="btn btn-default">
                <?= Yii::t('ElectionModule.base', 'Cancel') ?>
            </a>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
