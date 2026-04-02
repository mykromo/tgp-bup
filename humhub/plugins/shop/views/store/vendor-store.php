<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = Html::encode($vendor->shop_name);
?>
<div class="panel panel-default">
<div class="panel-heading">
    <strong><i class="fa fa-store"></i> <?= $this->title ?></strong>
    <?php if (!Yii::$app->user->isGuest): ?>
    <a href="<?= Url::to(['/shop/store/toggle-favorite', 'vendorId' => $vendor->id]) ?>" class="btn btn-<?= $isFavorited ? 'warning' : 'default' ?> btn-sm pull-right" data-method="post">
        <i class="fa fa-star<?= $isFavorited ? '' : '-o' ?>"></i> <?= $isFavorited ? 'Favorited' : 'Favorite' ?>
    </a>
    <?php endif; ?>
</div>
<div class="panel-body">
    <?php if ($vendor->description): ?><p class="text-muted"><?= Html::encode($vendor->description) ?></p><?php endif; ?>
    <?php if ($vendor->location): ?><p><i class="fa fa-map-marker"></i> <?= Html::encode($vendor->location) ?></p><?php endif; ?>
    <hr>
    <?php if (empty($products)): ?>
        <p class="text-muted text-center">No products available.</p>
    <?php else: ?>
    <div class="row">
    <?php foreach ($products as $p): ?>
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
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to Shop</a>
</div></div>
