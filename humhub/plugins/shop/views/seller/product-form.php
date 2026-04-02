<?php
use humhub\libs\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
$isNew = $model->isNewRecord;
$this->title = $isNew ? 'Add Product' : 'Edit Product';
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><?= $this->title ?></strong></div>
<div class="panel-body">
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
<?= $form->field($model, 'category_id')->dropDownList($categories, ['prompt' => '— Select Category —']) ?>
<div class="row">
    <div class="col-sm-4"><?= $form->field($model, 'price')->input('number', ['step' => '0.01', 'min' => '0']) ?></div>
    <div class="col-sm-4"><?= $form->field($model, 'sale_price')->input('number', ['step' => '0.01', 'min' => '0'])->hint('Leave empty for no sale') ?></div>
    <div class="col-sm-4"><?= $form->field($model, 'stock')->input('number', ['min' => '0'])->hint('Empty = unlimited') ?></div>
</div>
<div class="row">
    <div class="col-sm-4"><?= $form->field($model, 'sale_start')->input('datetime-local') ?></div>
    <div class="col-sm-4"><?= $form->field($model, 'sale_end')->input('datetime-local') ?></div>
    <div class="col-sm-4"><?= $form->field($model, 'location')->textInput(['maxlength' => 255]) ?></div>
</div>
<?= $form->field($model, 'sort_order')->input('number', ['min' => '0']) ?>
<?= $form->field($model, 'is_active')->checkbox() ?>

<hr><h4><i class="fa fa-image"></i> Product Images</h4>
<p class="help-block">Max 2MB each. JPG, PNG, WebP. Resized to 800x800.</p>
<?php if (!$isNew && $model->images): ?>
<div class="row" style="margin-bottom:10px">
    <?php foreach ($model->images as $img): ?>
    <div class="col-xs-3 col-sm-2" style="margin-bottom:8px;text-align:center">
        <img src="<?= $img->getUrl() ?>" style="max-width:100%;max-height:80px;border:1px solid #ddd;border-radius:4px">
        <br><a href="<?= Url::to(['/shop/seller/delete-image', 'id' => $img->id]) ?>" class="btn btn-danger btn-xs" style="margin-top:3px" data-method="post" data-confirm="Delete?"><i class="fa fa-trash"></i></a>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<input type="file" name="product_images[]" multiple accept=".jpg,.jpeg,.png,.webp" class="form-control">

<hr>
<?= Html::submitButton($isNew ? 'Add Product' : 'Save', ['class' => 'btn btn-primary']) ?>
<a href="<?= Url::to(['/shop/seller/dashboard']) ?>" class="btn btn-default">Cancel</a>
<?php ActiveForm::end(); ?>
</div></div>
