<?php
use humhub\libs\Html;
use yii\bootstrap\ActiveForm;
$cacheTtl = Yii::$app->getModule('shop')->settings->get('cacheTtl', 300);
?>
<div class="panel-body">
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'payment_instructions')->textarea(['rows' => 5])
    ->label(Yii::t('ShopModule.base', 'Payment Instructions'))
    ->hint(Yii::t('ShopModule.base', 'Displayed to buyers during checkout.')) ?>
<?= $form->field($model, 'accepted_methods')->textInput(['maxlength' => 500])
    ->label(Yii::t('ShopModule.base', 'Accepted Payment Methods'))
    ->hint(Yii::t('ShopModule.base', 'Comma-separated. e.g.: GCash,Bank Transfer,Cash,PayMaya')) ?>

<hr>
<h5><i class="fa fa-bolt"></i> <?= Yii::t('ShopModule.base', 'Performance') ?></h5>
<div class="form-group">
    <label><?= Yii::t('ShopModule.base', 'Cache Duration (seconds)') ?></label>
    <input type="number" name="cacheTtl" value="<?= (int) $cacheTtl ?>" min="0" max="86400" class="form-control" style="width:200px">
    <p class="help-block"><?= Yii::t('ShopModule.base', '0 = no caching. Default: 300. Max: 86400.') ?></p>
</div>

<hr>
<?= Html::submitButton(Yii::t('ShopModule.base', 'Save Settings'), ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>
</div>
