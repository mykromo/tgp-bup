<?php
use humhub\libs\Html;
use humhub\modules\stewardship\models\Fund;
use yii\bootstrap\ActiveForm;
$isNew = $model->isNewRecord;
$this->pageTitle = $isNew ? Yii::t('StewardshipModule.base', 'Create Fund') : Yii::t('StewardshipModule.base', 'Edit Fund');
?>
<div class="panel panel-default">
    <div class="panel-heading"><strong><?= $this->pageTitle ?></strong></div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
        <?= $form->field($model, 'fund_type')->dropDownList(Fund::getTypeLabels(), ['prompt' => '— Select —']) ?>
        <?= $form->field($model, 'description')->textarea(['rows' => 2]) ?>
        <?= $form->field($model, 'restriction_purpose')->textarea(['rows' => 2])
            ->hint(Yii::t('StewardshipModule.base', 'For restricted funds: describe what this money can be spent on. Expenses will be validated against this.')) ?>
        <hr>
        <?= Html::submitButton($isNew ? Yii::t('StewardshipModule.base', 'Create Fund') : Yii::t('StewardshipModule.base', 'Save'), ['class' => 'btn btn-primary']) ?>
        <a href="<?= $contentContainer->createUrl('/stewardship/dashboard/index') ?>" class="btn btn-default"><?= Yii::t('StewardshipModule.base', 'Cancel') ?></a>
        <?php ActiveForm::end(); ?>
    </div>
</div>
