<?php
use humhub\libs\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
$isNew = $model->isNewRecord;
$this->title = $isNew ? 'Create Discount Code' : 'Edit Discount Code';
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><?= $this->title ?></strong></div>
<div class="panel-body">
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'code')->textInput(['maxlength' => 50])->hint('Unique code buyers enter at checkout') ?>
<?= $form->field($model, 'type')->dropDownList(['percentage' => 'Percentage (%)', 'fixed' => 'Fixed Amount (₱)']) ?>
<?= $form->field($model, 'value')->input('number', ['step' => '0.01', 'min' => '0'])->hint('Discount value') ?>
<?= $form->field($model, 'min_order')->input('number', ['step' => '0.01', 'min' => '0'])->hint('Minimum order amount. Empty = no minimum.') ?>
<?= $form->field($model, 'max_uses')->input('number', ['min' => '1'])->hint('Empty = unlimited uses') ?>
<div class="row">
    <div class="col-sm-6"><?= $form->field($model, 'starts_at')->input('datetime-local') ?></div>
    <div class="col-sm-6"><?= $form->field($model, 'expires_at')->input('datetime-local') ?></div>
</div>
<?= $form->field($model, 'is_active')->checkbox() ?>
<hr>
<?= Html::submitButton($isNew ? 'Create Discount' : 'Save Discount', ['class' => 'btn btn-primary']) ?>
<a href="<?= Url::to(['/shop/seller/discounts']) ?>" class="btn btn-default">Cancel</a>
<?php ActiveForm::end(); ?>
</div></div>
