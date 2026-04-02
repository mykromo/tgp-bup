<?php
use humhub\libs\Html;
use yii\helpers\Url;
use humhub\modules\shop\models\DeliveryAddress;
$this->title = Yii::t('ShopModule.base', 'Checkout');
$addresses = DeliveryAddress::getForUser($user->id);
$defaultAddr = DeliveryAddress::getDefaultForUser($user->id);
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><i class="fa fa-credit-card"></i> <?= $this->title ?></strong></div>
<div class="panel-body">

<div class="well">
    <h4><?= Html::encode($product->name) ?></h4>
    <p class="text-muted"><?= Html::encode($product->description) ?></p>
    <h3 style="color:#337ab7"><?= $product->formatPrice() ?></h3>
</div>

<div class="alert alert-info">
    <strong><i class="fa fa-info-circle"></i> Payment Instructions</strong><br>
    <?= nl2br(Html::encode($settings->payment_instructions)) ?>
</div>

<?= Html::beginForm(Url::to(['/shop/store/buy', 'id' => $product->id]), 'post') ?>

<h4><i class="fa fa-map-marker"></i> Delivery Address</h4>
<?php if (empty($addresses)): ?>
    <div class="alert alert-warning">
        No delivery address saved. <a href="<?= Url::to(['/shop/address/create', 'return' => Url::to(['/shop/store/buy', 'id' => $product->id])]) ?>">Add one now</a>.
    </div>
<?php else: ?>
    <div class="form-group">
        <select name="address_id" class="form-control" required>
            <?php foreach ($addresses as $a): ?>
                <option value="<?= $a->id ?>" <?= ($defaultAddr && $defaultAddr->id == $a->id) ? 'selected' : '' ?>>
                    <?= Html::encode($a->getDropdownLabel()) ?> — <?= Html::encode($a->getFullAddress()) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="help-block"><a href="<?= Url::to(['/shop/address/create', 'return' => Url::to(['/shop/store/buy', 'id' => $product->id])]) ?>">+ Add new address</a></p>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Buyer</label>
            <p class="form-control-static"><?= Html::encode($user->displayName) ?> (<?= Html::encode($user->email) ?>)</p>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Quantity</label>
            <input type="number" name="quantity" value="1" min="1" class="form-control" style="width:100px">
        </div>
    </div>
</div>

<div class="form-group">
    <label>Payment Method <span class="text-danger">*</span></label>
    <select name="payment_method" class="form-control" required>
        <option value="">— Select —</option>
        <?php foreach ($settings->getMethodsList() as $m): ?>
            <option value="<?= Html::encode($m) ?>"><?= Html::encode($m) ?></option>
        <?php endforeach; ?>
    </select>
</div>

<div class="form-group">
    <label>Payment Reference Number <span class="text-danger">*</span></label>
    <input type="text" name="payment_reference" class="form-control" required placeholder="e.g. GCash ref #, bank transaction #">
    <p class="help-block">Enter the reference number from your payment transaction.</p>
</div>

<hr>
<?= Html::hiddenInput('confirm', '1') ?>
<?= Html::submitButton('<i class="fa fa-check"></i> Submit Order', ['class' => 'btn btn-success', 'disabled' => empty($addresses)]) ?>
<a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default">Cancel</a>
<?= Html::endForm() ?>
</div></div>
