<?php
use humhub\libs\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
$this->title = 'Add Variant: ' . Html::encode($product->name);
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><?= $this->title ?></strong></div>
<div class="panel-body">
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'name')->textInput(['maxlength' => 255])->hint('e.g. "Large", "Red", "Size 42"') ?>
<?= $form->field($model, 'sku')->textInput(['maxlength' => 100])->hint('Optional SKU code') ?>
<?= $form->field($model, 'price_adjustment')->input('number', ['step' => '0.01'])->hint('Amount added to base price. Use negative for discount.') ?>
<?= $form->field($model, 'stock')->input('number', ['min' => '0'])->hint('Empty = unlimited') ?>
<hr>
<?= Html::submitButton('Add Variant', ['class' => 'btn btn-primary']) ?>
<a href="<?= Url::to(['/shop/seller/variants', 'productId' => $product->id]) ?>" class="btn btn-default">Cancel</a>
<?php ActiveForm::end(); ?>
</div></div>
