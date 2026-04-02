<?php
use humhub\libs\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'Become a Seller');
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><i class="fa fa-store"></i> <?= $this->title ?></strong></div>
<div class="panel-body">

<div class="alert alert-info">
    <i class="fa fa-info-circle"></i>
    <?= Yii::t('ShopModule.base', 'To open a shop, you need to submit an application with the required documents. Your application will be reviewed by an administrator before you can start selling.') ?>
</div>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<?= $form->field($vendor, 'shop_name')->textInput(['maxlength' => 255])
    ->hint(Yii::t('ShopModule.base', 'The name that will appear on your shop and products.')) ?>
<?= $form->field($vendor, 'description')->textarea(['rows' => 3])
    ->hint(Yii::t('ShopModule.base', 'Describe what you plan to sell.')) ?>

<hr>
<h4><i class="fa fa-file-text"></i> <?= Yii::t('ShopModule.base', 'Required Documents') ?></h4>
<p class="help-block"><?= Yii::t('ShopModule.base', 'Upload clear copies of the following documents. Accepted formats: JPG, PNG, PDF, DOC. Max 5MB each.') ?></p>

<?php foreach ($requiredDocs as $type => $label): ?>
<div class="form-group">
    <label class="control-label"><?= Html::encode($label) ?> <span class="text-danger">*</span></label>
    <input type="file" name="doc_<?= $type ?>" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required>
</div>
<?php endforeach; ?>

<hr>
<?= Html::submitButton('<i class="fa fa-paper-plane"></i> ' . Yii::t('ShopModule.base', 'Submit Application'), ['class' => 'btn btn-primary']) ?>
<a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default"><?= Yii::t('ShopModule.base', 'Cancel') ?></a>

<?php ActiveForm::end(); ?>
</div></div>
