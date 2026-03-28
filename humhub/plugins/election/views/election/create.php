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
        <?= $form->field($election, 'expires_at')->input('datetime-local')
            ->hint(Yii::t('ElectionModule.base', 'Voting will automatically close after this date.')) ?>

        <p class="help-block">
            <i class="fa fa-info-circle"></i>
            <?= Yii::t('ElectionModule.base', 'After creating the election, chapter members can file their own candidacy for any position.') ?>
        </p>

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
