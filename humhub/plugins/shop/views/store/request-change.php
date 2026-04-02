<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = 'Request Change: ' . $order->order_number;
$item = $order->items[0] ?? null;
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><i class="fa fa-exchange"></i> <?= $this->title ?></strong></div>
<div class="panel-body">
<div class="alert alert-info">This order has been acknowledged by the seller. Changes require seller approval.</div>
<?= Html::beginForm('', 'post') ?>
<div class="form-group">
    <label>Request Type</label>
    <select name="request_type" class="form-control" style="width:200px">
        <option value="update">Update Order</option>
        <option value="cancel">Cancel Order</option>
    </select>
</div>
<div class="form-group">
    <label>New Delivery Address (optional)</label>
    <select name="new_address_id" class="form-control">
        <option value="">— Keep current —</option>
        <?php foreach ($addresses as $a): ?>
            <option value="<?= $a->id ?>"><?= Html::encode($a->getDropdownLabel()) ?></option>
        <?php endforeach; ?>
    </select>
</div>
<?php if ($item): ?>
<div class="form-group">
    <label>New Quantity (optional, current: <?= $item->quantity ?>)</label>
    <input type="number" name="new_quantity" value="" min="1" class="form-control" style="width:120px" placeholder="Leave empty to keep">
</div>
<?php endif; ?>
<div class="form-group">
    <label>Reason / Details</label>
    <textarea name="details" class="form-control" rows="3" placeholder="Explain why you need this change..."></textarea>
</div>
<hr>
<?= Html::submitButton('<i class="fa fa-paper-plane"></i> Submit Request', ['class' => 'btn btn-primary']) ?>
<a href="<?= Url::to(['/shop/store/my-orders']) ?>" class="btn btn-default">Cancel</a>
<?= Html::endForm() ?>
</div></div>
