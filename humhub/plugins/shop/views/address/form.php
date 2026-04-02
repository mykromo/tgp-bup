<?php
use humhub\libs\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
$isNew = $model->isNewRecord;
$this->title = $isNew ? 'Add Delivery Address' : 'Edit Delivery Address';
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><?= $this->title ?></strong></div>
<div class="panel-body">
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'label')->textInput(['maxlength' => 100])->hint('e.g. Home, Office, Dorm') ?>
<?= $form->field($model, 'recipient_name')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'phone')->textInput(['maxlength' => 50]) ?>
<?= $form->field($model, 'address_line1')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'address_line2')->textInput(['maxlength' => 255]) ?>
<div class="row">
    <div class="col-sm-4"><?= $form->field($model, 'city')->textInput(['maxlength' => 100]) ?></div>
    <div class="col-sm-4"><?= $form->field($model, 'province')->textInput(['maxlength' => 100]) ?></div>
    <div class="col-sm-4"><?= $form->field($model, 'postal_code')->textInput(['maxlength' => 20]) ?></div>
</div>
<?= $form->field($model, 'country')->textInput(['maxlength' => 100]) ?>
<?= $form->field($model, 'is_default')->checkbox() ?>
<hr>
<?= Html::submitButton($isNew ? 'Save Address' : 'Update', ['class' => 'btn btn-primary']) ?>
<a href="<?= Url::to(['/shop/address/index']) ?>" class="btn btn-default">Cancel</a>
<?php ActiveForm::end(); ?>
</div></div>
