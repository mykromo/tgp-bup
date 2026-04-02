<?php
use humhub\libs\Html;
use humhub\modules\shop\models\Order;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'Order') . ' ' . $order->order_number;
?>
<div class="panel panel-default">
<div class="panel-heading">
    <strong><?= $this->title ?></strong>
    <span class="label label-<?= Order::getStatusBadge($order->status) ?> pull-right"><?= Order::getStatusLabels()[$order->status] ?></span>
</div>
<div class="panel-body">
<div class="row">
    <div class="col-sm-6">
        <h5>Buyer</h5>
        <p><?= Html::encode($order->buyer_name) ?><br><small class="text-muted"><?= Html::encode($order->buyer_email) ?></small></p>
        <h5>Payment</h5>
        <p><strong>Method:</strong> <?= Html::encode($order->payment_method) ?><br>
        <strong>Reference:</strong> <code><?= Html::encode($order->payment_reference) ?></code><br>
        <strong>Date:</strong> <?= $order->payment_date ? Yii::$app->formatter->asDatetime($order->payment_date) : '—' ?></p>
        <?php if ($order->payment_verified): ?>
        <p class="text-success"><i class="fa fa-check-circle"></i> Verified by <?= Html::encode($order->verifier->displayName ?? 'Admin') ?> on <?= Yii::$app->formatter->asDatetime($order->verified_at) ?></p>
        <?php endif; ?>
    </div>
    <div class="col-sm-6">
        <h5>Order Summary</h5>
        <table class="table table-condensed">
        <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
        <tbody>
        <?php foreach ($order->items as $item): ?>
        <tr><td><?= Html::encode($item->product_name) ?></td><td><?= $item->quantity ?></td>
            <td>₱<?= number_format($item->unit_price, 2) ?></td><td>₱<?= number_format($item->total_price, 2) ?></td></tr>
        <?php endforeach; ?>
        <tr class="active"><td colspan="3" class="text-right"><strong>Total</strong></td><td><strong><?= $order->formatTotal() ?></strong></td></tr>
        </tbody></table>
    </div>
</div>
<hr>
<a href="<?= Url::to(['/shop/admin/orders']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
<?php if ($order->status === Order::STATUS_PAID): ?>
    <a href="<?= Url::to(['/shop/admin/verify-order', 'id' => $order->id]) ?>" class="btn btn-success pull-right" data-method="post" data-confirm="Verify this payment?"><i class="fa fa-check"></i> Verify Payment</a>
<?php endif; ?>
<?php if (in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_PAID])): ?>
    <a href="<?= Url::to(['/shop/admin/cancel-order', 'id' => $order->id]) ?>" class="btn btn-danger pull-right" style="margin-right:5px" data-method="post" data-confirm="Cancel this order?"><i class="fa fa-times"></i> Cancel Order</a>
<?php endif; ?>
</div></div>
