<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'My Wishlist');
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><i class="fa fa-heart"></i> <?= $this->title ?></strong></div>
<div class="panel-body">
<?php if (empty($items)): ?>
    <p class="text-muted text-center">Your wishlist is empty.</p>
<?php else: ?>
<div class="row">
<?php foreach ($items as $w): $p = $w->product; if (!$p) continue; ?>
    <div class="col-sm-6 col-md-4" style="margin-bottom:15px">
        <div class="panel panel-default">
            <?php $img = $p->getFirstImageUrl(); if ($img): ?>
            <div style="height:120px;overflow:hidden;background:#f5f5f5;text-align:center">
                <img src="<?= Html::encode($img) ?>" style="max-height:120px;max-width:100%">
            </div>
            <?php endif; ?>
            <div class="panel-body">
                <h5><a href="<?= Url::to(['/shop/store/view', 'id' => $p->id]) ?>"><?= Html::encode($p->name) ?></a></h5>
                <p><?= $p->formatPrice() ?></p>
                <a href="<?= Url::to(['/shop/store/toggle-wishlist', 'productId' => $p->id]) ?>" class="btn btn-danger btn-xs" data-method="post"><i class="fa fa-heart-o"></i> Remove</a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
<?php endif; ?>
<a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to Shop</a>
</div></div>
