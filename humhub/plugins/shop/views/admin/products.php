<?php
use humhub\libs\Html;
use yii\helpers\Url;
?>
<div class="panel-body">
<div style="margin-bottom:15px">
    <a href="<?= Url::to(['/shop/admin/index']) ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
</div>
<?php if (empty($products)): ?>
    <p class="text-muted text-center">No products yet.</p>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover">
<thead><tr><th>Product</th><th>Store</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
<tbody>
<?php foreach ($products as $p): ?>
<tr class="<?= !$p->is_active ? 'text-muted' : '' ?>">
    <td><?= Html::encode($p->name) ?></td>
    <td>
        <?php if ($p->vendor): ?>
            <a href="<?= Url::to(['/shop/store/vendor-store', 'id' => $p->vendor_id]) ?>" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;color:inherit">
                <?php $vLogo = $p->vendor->logo_path ? Yii::getAlias('@web') . '/' . $p->vendor->logo_path : ''; ?>
                <?php if ($vLogo): ?>
                    <img src="<?= Html::encode($vLogo) ?>" style="width:22px;height:22px;border-radius:2px;object-fit:cover" alt="">
                <?php endif; ?>
                <span><?= Html::encode($p->vendor->shop_name) ?></span>
            </a>
        <?php else: ?>
            <span class="text-muted">System</span>
        <?php endif; ?>
    </td>
    <td><?= $p->category ? Html::encode($p->category->name) : '—' ?></td>
    <td><?= $p->formatPrice() ?></td>
    <td><?= $p->stock !== null ? $p->stock : '∞' ?></td>
    <td><span class="label label-<?= $p->is_active ? 'success' : 'default' ?>"><?= $p->is_active ? 'Active' : 'Inactive' ?></span></td>
    <td class="text-right">
        <a href="<?= Url::to(['/shop/store/view', 'id' => $p->id]) ?>" class="btn btn-default btn-xs" target="_blank" title="View"><i class="fa fa-eye"></i></a>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
<?php endif; ?>
</div>
