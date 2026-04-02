<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'Seller Dashboard');
?>
<div class="panel panel-default">
<div class="panel-heading">
    <strong><i class="fa fa-store"></i> <?= Html::encode($vendor->shop_name) ?></strong>
    <div class="pull-right">
        <a href="<?= Url::to(['/shop/seller/create-product']) ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?= Yii::t('ShopModule.base', 'Add Product') ?></a>
        <a href="<?= Url::to(['/shop/seller/orders']) ?>" class="btn btn-default btn-sm"><i class="fa fa-list"></i> <?= Yii::t('ShopModule.base', 'Orders') ?></a>
        <a href="<?= Url::to(['/shop/seller/requests']) ?>" class="btn btn-default btn-sm"><i class="fa fa-exchange"></i> <?= Yii::t('ShopModule.base', 'Requests') ?></a>
        <a href="<?= Url::to(['/shop/seller/discounts']) ?>" class="btn btn-default btn-sm"><i class="fa fa-tag"></i> <?= Yii::t('ShopModule.base', 'Discounts') ?></a>
    </div>
</div>
<div class="panel-body">
<?php if (empty($products)): ?>
    <p class="text-muted text-center">No products yet. Add your first product!</p>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover">
<thead><tr><th>Product</th><th>Price</th><th>Sale</th><th>Stock</th><th>Variants</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
<tbody>
<?php foreach ($products as $p): ?>
<tr>
    <td><?= Html::encode($p->name) ?></td>
    <td>₱<?= number_format($p->price, 2) ?></td>
    <td><?= $p->isOnSale() ? '<span class="text-danger">₱' . number_format($p->sale_price, 2) . '</span>' : '—' ?></td>
    <td><?= $p->stock !== null ? $p->stock : '∞' ?></td>
    <td><?= count($p->variants) ?></td>
    <td><span class="label label-<?= $p->is_active ? 'success' : 'default' ?>"><?= $p->is_active ? 'Active' : 'Inactive' ?></span></td>
    <td class="text-right" style="white-space:nowrap">
        <a href="<?= Url::to(['/shop/seller/edit-product', 'id' => $p->id]) ?>" class="btn btn-default btn-xs"><i class="fa fa-pencil"></i></a>
        <a href="<?= Url::to(['/shop/seller/variants', 'productId' => $p->id]) ?>" class="btn btn-default btn-xs"><i class="fa fa-list"></i> Variants</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
<a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to Shop</a>
</div></div>
