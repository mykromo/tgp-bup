<?php
use humhub\libs\Html;
$cc = $contentContainer;
$this->title = Html::encode($product->name);
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><?= $this->title ?></strong></div>
<div class="panel-body">
    <?php if ($product->image_url): ?>
    <div style="text-align:center;margin-bottom:15px"><img src="<?= Html::encode($product->image_url) ?>" style="max-height:300px;max-width:100%"></div>
    <?php endif; ?>
    <p><?= nl2br(Html::encode($product->description)) ?></p>
    <h3 style="color:#337ab7"><?= $product->formatPrice() ?></h3>
    <?php if ($product->stock !== null): ?><p class="text-muted"><?= $product->stock ?> in stock</p><?php endif; ?>
    <?php if ($product->isInStock()): ?>
        <a href="<?= $cc->createUrl('/shop/store/buy', ['id' => $product->id]) ?>" class="btn btn-success"><i class="fa fa-shopping-cart"></i> Buy Now</a>
    <?php else: ?>
        <span class="label label-danger">Out of Stock</span>
    <?php endif; ?>
    <a href="<?= $cc->createUrl('/shop/store/index') ?>" class="btn btn-default">Back to Shop</a>
</div></div>
