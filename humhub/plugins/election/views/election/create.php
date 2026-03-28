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
            <h5><i class="fa fa-calendar"></i> <?= Yii::t('ElectionModule.base', 'Election Timeline') ?></h5>
            <p class="help-block">
                <?= Yii::t('ElectionModule.base', 'Phase 1: Members file candidacy until the deadline. Phase 2: Voting opens automatically after candidacy closes and runs until the voting deadline.') ?>
            </p>

            <?= $form->field($election, 'candidacy_expires_at')->input('datetime-local')
                ->label(Yii::t('ElectionModule.base', 'Filing of Candidacy Deadline')) ?>

            <?= $form->field($election, 'voting_expires_at')->input('datetime-local')
                ->label(Yii::t('ElectionModule.base', 'Voting Deadline')) ?>
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
