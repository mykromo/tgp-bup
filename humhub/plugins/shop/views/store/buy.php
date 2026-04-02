<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'Checkout');
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><i class="fa fa-credit-card"></i> <?= Yii::t('ShopModule.base', 'Checkout') ?></strong></div>
<div class="panel-body">

<div class="well">
    <h4><?= Html::encode($product->name) ?></h4>
    <p class="text-muted"><?= Html::encode($product->description) ?></p>
    <h3 style="color:#337ab7"><?= $product->formatPrice() ?></h3>
</div>

<div class="alert alert-info">
    <strong><i class="fa fa-info-circle"></i> <?= Yii::t('ShopModule.base', 'Payment Instructions') ?></strong><br>
    <?= nl2br(Html::encode($settings->payment_instructions)) ?>
</div>

<?= Html::beginForm(Url::to(['/shop/store/buy', 'id' => $product->id]), 'post') ?>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label><?= Yii::t('ShopModule.base', 'Buyer') ?></label>
            <p class="form-control-static"><?= Html::encode($user->displayName) ?> (<?= Html::encode($user->email) ?>)</p>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label><?= Yii::t('ShopModule.base', 'Quantity') ?></label>
            <input type="number" name="quantity" value="1" min="1" class="form-control" style="width:100px">
        </div>
    </div>
</div>

<div class="form-group">
    <label><?= Yii::t('ShopModule.base', 'Payment Method') ?> <span class="text-danger">*</span></label>
    <select name="payment_method" class="form-control" required>
        <option value="">— Select —</option>
        <?php foreach ($settings->getMethodsList() as $m): ?>
            <option value="<?= Html::encode($m) ?>"><?= Html::encode($m) ?></option>
        <?php endforeach; ?>
    </select>
</div>

<div class="form-group">
    <label><?= Yii::t('ShopModule.base', 'Payment Reference Number') ?> <span class="text-danger">*</span></label>
    <input type="text" name="payment_reference" class="form-control" required
           placeholder="<?= Yii::t('ShopModule.base', 'e.g. GCash ref #, bank transaction #, receipt #') ?>">
    <p class="help-block"><?= Yii::t('ShopModule.base', 'Enter the reference number from your payment transaction.') ?></p>
</div>

<hr>
<?= Html::hiddenInput('confirm', '1') ?>
<?= Html::submitButton('<i class="fa fa-check"></i> ' . Yii::t('ShopModule.base', 'Submit Order'), ['class' => 'btn btn-success']) ?>
<a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default"><?= Yii::t('ShopModule.base', 'Cancel') ?></a>

<?= Html::endForm() ?>
</div></div>
