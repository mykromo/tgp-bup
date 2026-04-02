<?php
use humhub\libs\Html;
use humhub\modules\shop\models\Order;
use yii\helpers\Url;
$this->title = 'Order ' . $order->order_number;
?>
<div class="panel panel-default">
<div class="panel-heading">
    <strong><?= $this->title ?></strong>
    <span class="label label-<?= Order::getStatusBadge($order->status) ?> pull-right"><?= Order::getStatusLabels()[$order->status] ?></span>
</div>
<div class="panel-body">
<div class="row">
<div class="col-sm-4">
    <h5>Buyer</h5>
    <p><?= Html::encode($order->buyer_name) ?><br><small class="text-muted"><?= Html::encode($order->buyer_email) ?></small></p>
    <a href="<?= $messengerUrl ?>" class="btn btn-info btn-xs" data-pjax-prevent><i class="fa fa-envelope"></i> Message Buyer</a>
</div>
<div class="col-sm-4">
    <h5>Delivery Address</h5>
    <?php if ($order->delivery_address): ?>
        <p style="font-size:12px"><?= nl2br(Html::encode($order->delivery_address)) ?></p>
    <?php else: ?>
        <p class="text-muted">No delivery address provided.</p>
    <?php endif; ?>
</div>
<div class="col-sm-4">
    <h5>Payment</h5>
    <p><strong>Method:</strong> <?= Html::encode($order->payment_method) ?><br>
    <strong>Ref:</strong> <code><?= Html::encode($order->payment_reference) ?></code></p>
</div>
</div>

<h5>Items</h5>
<table class="table table-condensed">
<thead><tr><th>Item</th><th>Variant</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
<tbody>
<?php foreach ($order->items as $item): ?>
<tr><td><?= Html::encode($item->product_name) ?></td><td><?= Html::encode($item->variant_name ?? '—') ?></td><td><?= $item->quantity ?></td>
    <td>₱<?= number_format($item->unit_price, 2) ?></td><td>₱<?= number_format($item->total_price, 2) ?></td></tr>
<?php endforeach; ?>
<tr class="active"><td colspan="4" class="text-right"><strong>Total</strong></td><td><strong><?= $order->formatTotal() ?></strong></td></tr>
</tbody></table>

<?php if ($order->rejection_reason): ?>
<div class="alert alert-danger"><strong>Rejection Reason:</strong> <?= Html::encode($order->rejection_reason) ?></div>
<?php endif; ?>

<hr>
<a href="<?= Url::to(['/shop/seller/orders']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>

<?php if ($order->status === Order::STATUS_PAID): ?>
<div class="pull-right">
    <a href="<?= Url::to(['/shop/seller/verify-order', 'id' => $order->id]) ?>" class="btn btn-success" data-method="post" data-confirm="Verify this payment?"><i class="fa fa-check"></i> Verify</a>
    <?= Html::beginForm(Url::to(['/shop/seller/reject-order', 'id' => $order->id]), 'post', ['style' => 'display:inline']) ?>
    <div class="input-group" style="display:inline-table;width:auto">
        <input type="text" name="reason" class="form-control input-sm" placeholder="Rejection reason..." style="width:200px">
        <span class="input-group-btn"><?= Html::submitButton('<i class="fa fa-times"></i> Reject', ['class' => 'btn btn-danger btn-sm', 'data-confirm' => 'Reject this order?']) ?></span>
    </div>
    <?= Html::endForm() ?>
</div>
<?php endif; ?>

</div></div>
