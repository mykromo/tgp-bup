<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'Manage Products');
?>
<div class="panel panel-default">
<div class="panel-heading">
    <strong><i class="fa fa-cube"></i> <?= $this->title ?></strong>
    <div class="pull-right">
        <a href="<?= Url::to(['/shop/admin/create-product']) ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?= Yii::t('ShopModule.base', 'Add Product') ?></a>
        <a href="<?= Url::to(['/shop/vendor/applications']) ?>" class="btn btn-default btn-sm"><i class="fa fa-users"></i> <?= Yii::t('ShopModule.base', 'Applications') ?></a>
        <a href="<?= Url::to(['/shop/admin/orders']) ?>" class="btn btn-default btn-sm"><i class="fa fa-list"></i> <?= Yii::t('ShopModule.base', 'Orders') ?></a>
        <a href="<?= Url::to(['/shop/admin/settings']) ?>" class="btn btn-default btn-sm"><i class="fa fa-cog"></i> <?= Yii::t('ShopModule.base', 'Settings') ?></a>
    </div>
</div>
<div class="panel-body">
<?php if (empty($products)): ?>
    <p class="text-muted text-center">No products yet.</p>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover">
<thead><tr><th>Product</th><th>Price</th><th>Stock</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
<tbody>
<?php foreach ($products as $p): ?>
<tr class="<?= !$p->is_active ? 'text-muted' : '' ?>">
    <td><?= Html::encode($p->name) ?></td>
    <td><?= $p->formatPrice() ?></td>
    <td><?= $p->stock !== null ? $p->stock : 'Unlimited' ?></td>
    <td><span class="label label-<?= $p->is_active ? 'success' : 'default' ?>"><?= $p->is_active ? 'Active' : 'Inactive' ?></span></td>
    <td class="text-right">
        <a href="<?= Url::to(['/shop/admin/edit-product', 'id' => $p->id]) ?>" class="btn btn-default btn-xs"><i class="fa fa-pencil"></i> Edit</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
<a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?= Yii::t('ShopModule.base', 'Back to Shop') ?></a>
</div></div>
