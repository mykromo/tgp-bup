<?php
use humhub\libs\Html;
use humhub\modules\shop\models\Order;
use humhub\modules\shop\models\OrderRequest;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'My Orders');
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><i class="fa fa-list"></i> <?= $this->title ?></strong></div>
<div class="panel-body">
<?php if (empty($orders)): ?>
    <p class="text-muted text-center">No orders yet.</p>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover">
<thead><tr><th>Order #</th><th>Date</th><th>Total</th><th>Ref</th><th>Status</th><th></th></tr></thead>
<tbody>
<?php foreach ($orders as $o): $hasPending = OrderRequest::hasPending($o->id); ?>
<tr>
    <td><?= Html::encode($o->order_number) ?></td>
    <td><?= Yii::$app->formatter->asDatetime($o->created_at) ?></td>
    <td><?= $o->formatTotal() ?></td>
    <td><code><?= Html::encode($o->payment_reference) ?></code></td>
    <td>
        <span class="label label-<?= Order::getStatusBadge($o->status) ?>"><?= Order::getStatusLabels()[$o->status] ?? $o->status ?></span>
        <?php if ($hasPending): ?><span class="label label-warning">Request Pending</span><?php endif; ?>
    </td>
    <td style="white-space:nowrap">
        <a href="<?= Url::to(['/shop/store/download-receipt', 'id' => $o->id]) ?>" class="btn btn-default btn-xs" data-pjax-prevent title="Download Receipt"><i class="fa fa-download"></i></a>
        <?php if ($o->status === Order::STATUS_PAID): ?>
            <a href="<?= Url::to(['/shop/store/edit-order', 'id' => $o->id]) ?>" class="btn btn-default btn-xs"><i class="fa fa-pencil"></i> Edit</a>
            <a href="<?= Url::to(['/shop/store/cancel-order', 'id' => $o->id]) ?>" class="btn btn-danger btn-xs" data-method="post" data-confirm="Cancel this order?"><i class="fa fa-times"></i> Cancel</a>
        <?php elseif ($o->status === Order::STATUS_VERIFIED && !$hasPending): ?>
            <a href="<?= Url::to(['/shop/store/request-change', 'id' => $o->id]) ?>" class="btn btn-info btn-xs"><i class="fa fa-exchange"></i> Request Change</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
<?php endif; ?>
<a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to Shop</a>
</div></div>
