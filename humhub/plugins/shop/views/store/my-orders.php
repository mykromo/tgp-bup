<?php
use humhub\libs\Html;
use humhub\modules\shop\models\Order;
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
<thead><tr><th>Order #</th><th>Date</th><th>Total</th><th>Payment Ref</th><th>Status</th></tr></thead>
<tbody>
<?php foreach ($orders as $o): ?>
<tr>
    <td><?= Html::encode($o->order_number) ?></td>
    <td><?= Yii::$app->formatter->asDatetime($o->created_at) ?></td>
    <td><?= $o->formatTotal() ?></td>
    <td><?= Html::encode($o->payment_reference) ?></td>
    <td><span class="label label-<?= Order::getStatusBadge($o->status) ?>"><?= Order::getStatusLabels()[$o->status] ?? $o->status ?></span></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
<?php endif; ?>
<a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?= Yii::t('ShopModule.base', 'Back to Shop') ?></a>
</div></div>
