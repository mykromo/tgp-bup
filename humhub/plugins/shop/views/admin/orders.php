<?php
use humhub\libs\Html;
use humhub\modules\shop\models\Order;
use yii\helpers\Url;
?>
<div class="panel-body">
<div style="margin-bottom:15px">
    <a href="<?= Url::to(['/shop/admin/index']) ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
</div>
<form method="get" action="<?= Url::to(['/shop/admin/orders']) ?>" class="form-inline" style="margin-bottom:15px">
    <select name="status" class="form-control input-sm" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <?php foreach (Order::getStatusLabels() as $k => $v): ?>
            <option value="<?= $k ?>" <?= ($selectedStatus ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
    </select>
    <span class="text-muted" style="margin-left:10px"><?= count($orders) ?> order(s)</span>
</form>
<?php if (empty($orders)): ?>
    <p class="text-muted text-center">No orders found.</p>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover">
<thead><tr><th>Order #</th><th>Store</th><th>Buyer</th><th>Total</th><th>Ref</th><th>Status</th><th>Date</th><th></th></tr></thead>
<tbody>
<?php foreach ($orders as $o):
    $orderVendor = null;
    if (!empty($o->items)) {
        $firstItem = $o->items[0];
        if ($firstItem->product && $firstItem->product->vendor) {
            $orderVendor = $firstItem->product->vendor;
        }
    }
?>
<tr>
    <td><code><?= Html::encode($o->order_number) ?></code></td>
    <td>
        <?php if ($orderVendor): ?>
            <a href="<?= Url::to(['/shop/store/vendor-store', 'id' => $orderVendor->id]) ?>" style="display:inline-flex;align-items:center;gap:5px;text-decoration:none;color:inherit">
                <?php $vLogo = $orderVendor->logo_path ? Yii::getAlias('@web') . '/' . $orderVendor->logo_path : ''; ?>
                <?php if ($vLogo): ?>
                    <img src="<?= Html::encode($vLogo) ?>" style="width:20px;height:20px;border-radius:2px;object-fit:cover" alt="">
                <?php endif; ?>
                <span><?= Html::encode($orderVendor->shop_name) ?></span>
            </a>
        <?php else: ?>
            <span class="text-muted">—</span>
        <?php endif; ?>
    </td>
    <td><?php if ($o->user): ?><a href="<?= $o->user->getUrl() ?>"><?= Html::encode($o->user->displayName) ?></a><?php else: ?><?= Html::encode($o->buyer_name) ?><?php endif; ?></td>
    <td><?= $o->formatTotal() ?></td>
    <td><code style="font-size:11px"><?= Html::encode($o->payment_reference) ?></code></td>
    <td><span class="label label-<?= Order::getStatusBadge($o->status) ?>"><?= Order::getStatusLabels()[$o->status] ?? $o->status ?></span></td>
    <td style="white-space:nowrap;font-size:12px"><?= Yii::$app->formatter->asDatetime($o->created_at) ?></td>
    <td><a href="<?= Url::to(['/shop/admin/view-order', 'id' => $o->id]) ?>" class="btn btn-default btn-xs">View</a></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
<?php endif; ?>
</div>
