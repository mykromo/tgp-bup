<?php

use humhub\libs\Html;
use humhub\modules\election\models\ElectionPosition;
use humhub\modules\content\components\ContentContainerActiveRecord;
use yii\bootstrap\ActiveForm;

/* @var $model ElectionPosition */
/* @var $contentContainer ContentContainerActiveRecord */

$isNew = $model->isNewRecord;
$this->pageTitle = $isNew
    ? Yii::t('ElectionModule.base', 'Add Position')
    : Yii::t('ElectionModule.base', 'Edit Position');
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <strong><?= $isNew
            ? Yii::t('ElectionModule.base', 'Add Officer Position')
            : Yii::t('ElectionModule.base', 'Edit Officer Position') ?></strong>
    </div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin(['id' => 'position-form']); ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>
        <?= $form->field($model, 'sort_order')->input('number', ['min' => 0])
            ->hint(Yii::t('ElectionModule.base', 'Lower numbers appear first.')) ?>

        <hr>
        <div class="form-group">
            <?= Html::submitButton(
                $isNew ? Yii::t('ElectionModule.base', 'Add Position') : Yii::t('ElectionModule.base', 'Save'),
                ['class' => 'btn btn-primary']
            ) ?>
            <a href="<?= $contentContainer->createUrl('/election/position/index') ?>" class="btn btn-default">
                <?= Yii::t('ElectionModule.base', 'Cancel') ?>
            </a>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
