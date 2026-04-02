<?php
use humhub\libs\Html;
use humhub\modules\shop\models\Order;
use yii\helpers\Url;
$this->title = 'My Orders';
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><i class="fa fa-list"></i> <?= $this->title ?></strong></div>
<div class="panel-body">
<form method="get" action="<?= Url::to(['/shop/seller/orders']) ?>" class="form-inline" style="margin-bottom:15px">
    <select name="status" class="form-control input-sm" onchange="this.form.submit()">
        <option value="">All</option>
        <?php foreach (Order::getStatusLabels() as $k => $v): ?>
            <option value="<?= $k ?>" <?= ($selectedStatus ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
    </select>
</form>
<?php if (empty($orders)): ?>
    <p class="text-muted text-center">No orders.</p>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover">
<thead><tr><th>Order #</th><th>Buyer</th><th>Total</th><th>Delivery</th><th>Status</th><th>Date</th><th></th></tr></thead>
<tbody>
<?php foreach ($orders as $o): ?>
<tr>
    <td><?= Html::encode($o->order_number) ?></td>
    <td><?= $o->user ? Html::encode($o->user->displayName) : Html::encode($o->buyer_name) ?></td>
    <td><?= $o->formatTotal() ?></td>
    <td style="max-width:200px;font-size:11px"><?= $o->delivery_address ? nl2br(Html::encode($o->delivery_address)) : '<span class="text-muted">No address</span>' ?></td>
    <td><span class="label label-<?= Order::getStatusBadge($o->status) ?>"><?= Order::getStatusLabels()[$o->status] ?? $o->status ?></span></td>
    <td style="white-space:nowrap"><?= Yii::$app->formatter->asDate($o->created_at) ?></td>
    <td><a href="<?= Url::to(['/shop/seller/view-order', 'id' => $o->id]) ?>" class="btn btn-default btn-xs">View</a></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
<?php endif; ?>
<a href="<?= Url::to(['/shop/seller/dashboard']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
</div></div>
