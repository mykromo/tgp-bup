<?php
use humhub\libs\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'Shop Settings');
$cacheTtl = Yii::$app->getModule('shop')->settings->get('cacheTtl', 300);
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
<h4><i class="fa fa-bolt"></i> <?= Yii::t('ShopModule.base', 'Performance') ?></h4>
<div class="form-group">
    <label><?= Yii::t('ShopModule.base', 'Cache Duration (seconds)') ?></label>
    <input type="number" name="cacheTtl" value="<?= (int) $cacheTtl ?>" min="0" max="86400" class="form-control" style="width:200px">
    <p class="help-block"><?= Yii::t('ShopModule.base', 'How long to cache product and category data. 0 = no caching. Default: 300 (5 minutes). Max: 86400 (24 hours).') ?></p>
</div>

<hr>
<?= Html::submitButton(Yii::t('ShopModule.base', 'Save Settings'), ['class' => 'btn btn-primary']) ?>
<a href="<?= Url::to(['/shop/admin/products']) ?>" class="btn btn-default">Back</a>
<?php ActiveForm::end(); ?>
</div></div>
