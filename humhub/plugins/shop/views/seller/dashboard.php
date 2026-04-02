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
<thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Sale</th><th>Stock</th><th>Variants</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
<tbody>
<?php foreach ($products as $p): ?>
<tr class="<?= !$p->is_active ? 'text-muted' : '' ?>">
    <td><?= Html::encode($p->name) ?></td>
    <td><?= $p->category ? Html::encode($p->category->name) : '<span class="text-muted">—</span>' ?></td>
    <td>₱<?= number_format($p->price, 2) ?></td>
    <td>
        <?php if (method_exists($p, 'isOnSale') && $p->isOnSale()): ?>
            <span class="text-danger">₱<?= number_format($p->sale_price, 2) ?></span>
            <?php if ($p->sale_end): ?><br><small class="text-muted"><?= Yii::$app->formatter->asDate($p->sale_end) ?></small><?php endif; ?>
        <?php else: ?>
            —
        <?php endif; ?>
    </td>
    <td><?= $p->stock !== null ? $p->stock : '∞' ?></td>
    <td><?php try { echo count($p->variants); } catch (\Throwable $e) { echo '—'; } ?></td>
    <td><span class="label label-<?= $p->is_active ? 'success' : 'default' ?>"><?= $p->is_active ? 'Active' : 'Inactive' ?></span></td>
    <td class="text-right" style="white-space:nowrap">
        <a href="<?= Url::to(['/shop/seller/edit-product', 'id' => $p->id]) ?>" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-pencil"></i></a>
        <a href="<?= Url::to(['/shop/seller/variants', 'productId' => $p->id]) ?>" class="btn btn-default btn-xs" title="Variants"><i class="fa fa-list"></i></a>
        <a href="<?= Url::to(['/shop/seller/toggle-product', 'id' => $p->id]) ?>" class="btn btn-<?= $p->is_active ? 'warning' : 'success' ?> btn-xs" data-method="post" title="<?= $p->is_active ? 'Disable' : 'Enable' ?>"><i class="fa fa-<?= $p->is_active ? 'pause' : 'play' ?>"></i></a>
        <a href="<?= Url::to(['/shop/seller/delete-product', 'id' => $p->id]) ?>" class="btn btn-danger btn-xs" data-method="post" data-confirm="Delete this product? This cannot be undone." title="Delete"><i class="fa fa-trash"></i></a>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
<?php endif; ?>
<a href="<?= Url::to(['/shop/store/vendor-store', 'id' => $vendor->id]) ?>" class="btn btn-default"><i class="fa fa-eye"></i> View Store</a>
<a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to Shop</a>
</div></div>
