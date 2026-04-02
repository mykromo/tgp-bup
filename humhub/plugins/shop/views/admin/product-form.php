<?php
use humhub\libs\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
$isNew = $model->isNewRecord;
$this->title = $isNew ? Yii::t('ShopModule.base', 'Add Product') : Yii::t('ShopModule.base', 'Edit Product');
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><?= $this->title ?></strong></div>
<div class="panel-body">
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
<?= $form->field($model, 'price')->input('number', ['step' => '0.01', 'min' => '0']) ?>
<?= $form->field($model, 'stock')->input('number', ['min' => '0'])->hint('Leave empty for unlimited stock') ?>
<?= $form->field($model, 'sort_order')->input('number', ['min' => '0']) ?>
<?= $form->field($model, 'is_active')->checkbox() ?>

<hr>
<h4><i class="fa fa-image"></i> <?= Yii::t('ShopModule.base', 'Product Images') ?></h4>
<p class="help-block"><?= Yii::t('ShopModule.base', 'Upload up to 10 images. Max 2MB each. Images will be resized to 800x800px. Accepted: JPG, PNG, WebP.') ?></p>

<?php if (!$isNew && $model->images): ?>
<div class="row" style="margin-bottom:15px">
    <?php foreach ($model->images as $img): ?>
    <div class="col-xs-3 col-sm-2" style="margin-bottom:10px;text-align:center">
        <img src="<?= $img->getUrl() ?>" style="max-width:100%;max-height:100px;border:1px solid #ddd;border-radius:4px">
        <br>
        <a href="<?= Url::to(['/shop/admin/delete-image', 'id' => $img->id]) ?>"
           class="btn btn-danger btn-xs" style="margin-top:4px"
           data-method="post" data-confirm="Delete this image?">
            <i class="fa fa-trash"></i>
        </a>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="form-group">
    <label><?= Yii::t('ShopModule.base', 'Upload Images') ?></label>
    <input type="file" name="product_images[]" multiple accept=".jpg,.jpeg,.png,.webp" class="form-control">
</div>

<hr>
<?= Html::submitButton($isNew ? Yii::t('ShopModule.base', 'Add Product') : Yii::t('ShopModule.base', 'Save'), ['class' => 'btn btn-primary']) ?>
<a href="<?= Url::to(['/shop/admin/products']) ?>" class="btn btn-default">Cancel</a>
<?php ActiveForm::end(); ?>
</div></div>
