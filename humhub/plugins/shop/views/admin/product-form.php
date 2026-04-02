<?php
use humhub\libs\Html;
use yii\bootstrap\ActiveForm;
$isNew = $model->isNewRecord;
$cc = $contentContainer;
$this->title = $isNew ? Yii::t('ShopModule.base', 'Add Product') : Yii::t('ShopModule.base', 'Edit Product');
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><?= $this->title ?></strong></div>
<div class="panel-body">
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
<?= $form->field($model, 'price')->input('number', ['step' => '0.01', 'min' => '0']) ?>
<?= $form->field($model, 'stock')->input('number', ['min' => '0'])->hint('Leave empty for unlimited stock') ?>
<?= $form->field($model, 'image_url')->textInput(['maxlength' => 500])->hint('Direct URL to product image') ?>
<?= $form->field($model, 'sort_order')->input('number', ['min' => '0']) ?>
<?= $form->field($model, 'is_active')->checkbox() ?>
<hr>
<?= Html::submitButton($isNew ? Yii::t('ShopModule.base', 'Add Product') : Yii::t('ShopModule.base', 'Save'), ['class' => 'btn btn-primary']) ?>
<a href="<?= $cc->createUrl('/shop/admin/products') ?>" class="btn btn-default">Cancel</a>
<?php ActiveForm::end(); ?>
</div></div>
