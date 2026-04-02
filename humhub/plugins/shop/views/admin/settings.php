<?php
use humhub\libs\Html;
use yii\bootstrap\ActiveForm;
$cc = $contentContainer;
$this->title = Yii::t('ShopModule.base', 'Payment Settings');
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><i class="fa fa-cog"></i> <?= $this->title ?></strong></div>
<div class="panel-body">
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'payment_instructions')->textarea(['rows' => 5])
    ->label(Yii::t('ShopModule.base', 'Payment Instructions'))
    ->hint(Yii::t('ShopModule.base', 'Displayed to buyers during checkout. Include bank details, GCash number, etc.')) ?>
<?= $form->field($model, 'accepted_methods')->textInput(['maxlength' => 500])
    ->label(Yii::t('ShopModule.base', 'Accepted Payment Methods'))
    ->hint(Yii::t('ShopModule.base', 'Comma-separated list. e.g.: GCash,Bank Transfer,Cash,PayMaya')) ?>
<hr>
<?= Html::submitButton(Yii::t('ShopModule.base', 'Save Settings'), ['class' => 'btn btn-primary']) ?>
<a href="<?= $cc->createUrl('/shop/admin/products') ?>" class="btn btn-default">Back</a>
<?php ActiveForm::end(); ?>
</div></div>
