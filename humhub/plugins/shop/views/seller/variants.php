<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = 'Variants: ' . Html::encode($product->name);
?>
<div class="panel panel-default">
<div class="panel-heading">
    <strong><?= $this->title ?></strong>
    <a href="<?= Url::to(['/shop/seller/add-variant', 'productId' => $product->id]) ?>" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i> Add Variant</a>
</div>
<div class="panel-body">
<p class="help-block">Variants let you offer different sizes, colors, or options with price adjustments and separate stock tracking.</p>
<?php if (empty($variants)): ?>
    <p class="text-muted text-center">No variants yet.</p>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover">
<thead><tr><th>Name</th><th>SKU</th><th>Price Adj.</th><th>Effective Price</th><th>Stock</th><th></th></tr></thead>
<tbody>
<?php foreach ($variants as $v): ?>
<tr>
    <td><?= Html::encode($v->name) ?></td>
    <td><code><?= Html::encode($v->sku) ?></code></td>
    <td><?= $v->price_adjustment >= 0 ? '+' : '' ?>₱<?= number_format($v->price_adjustment, 2) ?></td>
    <td><?= $v->formatPrice() ?></td>
    <td><?= $v->stock !== null ? $v->stock : '∞' ?></td>
    <td><a href="<?= Url::to(['/shop/seller/delete-variant', 'id' => $v->id]) ?>" class="btn btn-danger btn-xs" data-method="post" data-confirm="Delete?"><i class="fa fa-trash"></i></a></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
<a href="<?= Url::to(['/shop/seller/dashboard']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
</div></div>
