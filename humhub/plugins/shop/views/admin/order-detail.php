<?php
use humhub\libs\Html;
use humhub\modules\shop\models\Order;
use yii\helpers\Url;
$orderVendor = null;
if (!empty($order->items)) {
    $firstItem = $order->items[0];
    if ($firstItem->product && $firstItem->product->vendor) {
        $orderVendor = $firstItem->product->vendor;
    }
}
?>
<div class="panel-body">
<h5>Order <?= Html::encode($order->order_number) ?>
    <span class="label label-<?= Order::getStatusBadge($order->status) ?>"><?= Order::getStatusLabels()[$order->status] ?></span>
</h5>

<div class="row">
    <div class="col-sm-4">
        <h5>Store</h5>
        <?php if ($orderVendor): ?>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
            <?php $vLogo = $orderVendor->logo_path ? Yii::getAlias('@web') . '/' . $orderVendor->logo_path : ''; ?>
            <?php if ($vLogo): ?>
                <img src="<?= Html::encode($vLogo) ?>" style="width:32px;height:32px;border-radius:3px;object-fit:cover" alt="">
            <?php endif; ?>
            <a href="<?= Url::to(['/shop/store/vendor-store', 'id' => $orderVendor->id]) ?>"><?= Html::encode($orderVendor->shop_name) ?></a>
        </div>
        <?php else: ?>
            <p class="text-muted">—</p>
        <?php endif; ?>

        <h5>Buyer</h5>
        <p>
            <?php if ($order->user): ?>
                <a href="<?= $order->user->getUrl() ?>"><?= Html::encode($order->user->displayName) ?></a>
            <?php else: ?>
                <?= Html::encode($order->buyer_name) ?>
            <?php endif; ?>
            <br><small class="text-muted"><?= Html::encode($order->buyer_email) ?></small>
        </p>
    </div>
    <div class="col-sm-4">
        <?php if ($order->delivery_address): ?>
            <h5>Delivery Address</h5>
            <p style="font-size:12px"><?= nl2br(Html::encode($order->delivery_address)) ?></p>
        <?php endif; ?>

        <h5>Payment</h5>
        <p>
            <strong>Method:</strong> <?= Html::encode($order->payment_method) ?><br>
            <strong>Reference:</strong> <code><?= Html::encode($order->payment_reference) ?></code><br>
            <strong>Date:</strong> <?= $order->payment_date ? Yii::$app->formatter->asDatetime($order->payment_date) : '—' ?>
        </p>
        <?php if ($order->payment_verified): ?>
        <p class="text-success"><i class="fa fa-check-circle"></i> Verified by <?= Html::encode($order->verifier ? $order->verifier->displayName : 'Admin') ?> on <?= Yii::$app->formatter->asDatetime($order->verified_at) ?></p>
        <?php endif; ?>
    </div>
    <div class="col-sm-4">
        <h5>Items</h5>
        <table class="table table-condensed">
        <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
        <tbody>
        <?php foreach ($order->items as $item): ?>
        <tr>
            <td><?= Html::encode($item->product_name) ?></td>
            <td><?= $item->quantity ?></td>
            <td>₱<?= number_format($item->unit_price, 2) ?></td>
            <td>₱<?= number_format($item->total_price, 2) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="active"><td colspan="3" class="text-right"><strong>Total</strong></td><td><strong><?= $order->formatTotal() ?></strong></td></tr>
        </tbody></table>
    </div>
</div>

<hr>
<a href="<?= Url::to(['/shop/admin/orders']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to Orders</a>
<?php if ($order->status === Order::STATUS_PAID): ?>
    <a href="<?= Url::to(['/shop/admin/verify-order', 'id' => $order->id]) ?>" class="btn btn-success pull-right" data-method="post" data-confirm="Verify this payment?"><i class="fa fa-check"></i> Verify Payment</a>
<?php endif; ?>
<?php if (in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_PAID])): ?>
    <a href="<?= Url::to(['/shop/admin/cancel-order', 'id' => $order->id]) ?>" class="btn btn-danger pull-right" style="margin-right:5px" data-method="post" data-confirm="Cancel this order?"><i class="fa fa-times"></i> Cancel Order</a>
<?php endif; ?>
</div>
