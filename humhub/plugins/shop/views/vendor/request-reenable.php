<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'Request Store Re-enablement');
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><i class="fa fa-unlock"></i> <?= $this->title ?></strong></div>
<div class="panel-body">

<div class="alert alert-warning">
    <strong><i class="fa fa-ban"></i> <?= Yii::t('ShopModule.base', 'Your store has been disabled') ?></strong>
    <?php if ($vendor->disabled_reason): ?>
        <p style="margin-top:8px"><?= Yii::t('ShopModule.base', 'Reason:') ?> <?= Html::encode($vendor->disabled_reason) ?></p>
    <?php endif; ?>
    <?php if ($vendor->disabled_at): ?>
        <p class="text-muted"><?= Yii::t('ShopModule.base', 'Disabled on:') ?> <?= Yii::$app->formatter->asDatetime($vendor->disabled_at) ?></p>
    <?php endif; ?>
</div>

<?php if ($vendor->reenable_request): ?>
<div class="alert alert-info">
    <strong><i class="fa fa-clock-o"></i> <?= Yii::t('ShopModule.base', 'You have already submitted a request') ?></strong>
    <p style="margin-top:8px"><?= Html::encode($vendor->reenable_request) ?></p>
    <p class="text-muted"><?= Yii::t('ShopModule.base', 'Submitted:') ?> <?= Yii::$app->formatter->asDatetime($vendor->reenable_requested_at) ?></p>
</div>
<?php else: ?>

<?= Html::beginForm('', 'post', ['enctype' => 'multipart/form-data']) ?>
<div class="form-group">
    <label><?= Yii::t('ShopModule.base', 'Explanation') ?> <span class="text-danger">*</span></label>
    <textarea name="explanation" class="form-control" rows="5" required
              placeholder="<?= Yii::t('ShopModule.base', 'Explain why your store should be re-enabled. Describe any corrective actions you have taken...') ?>"></textarea>
</div>
<div class="form-group">
    <label><?= Yii::t('ShopModule.base', 'Supporting Documents') ?></label>
    <input type="file" name="reenable_docs[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="form-control">
    <p class="help-block"><?= Yii::t('ShopModule.base', 'Upload proof or documents to support your request. Max 5MB each. JPG, PNG, PDF, DOC accepted.') ?></p>
</div>
<hr>
<?= Html::submitButton('<i class="fa fa-paper-plane"></i> ' . Yii::t('ShopModule.base', 'Submit Request'), ['class' => 'btn btn-primary']) ?>
<?= Html::endForm() ?>

<?php endif; ?>

<br>
<a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?= Yii::t('ShopModule.base', 'Back to Shop') ?></a>
</div></div>
