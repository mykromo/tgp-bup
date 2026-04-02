<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'Favorite Stores');
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><i class="fa fa-star"></i> <?= $this->title ?></strong></div>
<div class="panel-body">
<?php if (empty($items)): ?>
    <p class="text-muted text-center">No favorite stores yet.</p>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover">
<thead><tr><th>Store</th><th>Location</th><th></th></tr></thead>
<tbody>
<?php foreach ($items as $f): $v = $f->vendor; if (!$v) continue; ?>
<tr>
    <td><a href="<?= Url::to(['/shop/store/vendor-store', 'id' => $v->id]) ?>"><?= Html::encode($v->shop_name) ?></a></td>
    <td><?= Html::encode($v->location ?? '—') ?></td>
    <td><a href="<?= Url::to(['/shop/store/toggle-favorite', 'vendorId' => $v->id]) ?>" class="btn btn-warning btn-xs" data-method="post"><i class="fa fa-star-o"></i> Remove</a></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
<a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to Shop</a>
</div></div>
