<?php
use humhub\libs\Html;
use yii\helpers\Url;
use humhub\modules\shop\models\Wishlist;
/* @var $p \humhub\modules\shop\models\Product */
$img = $p->getFirstImageUrl();
$onSale = $p->isOnSale();
$saleEnds = $onSale && $p->sale_end ? strtotime($p->sale_end) : null;
$uid = Yii::$app->user->isGuest ? 0 : Yii::$app->user->id;
$wishlisted = $uid ? Wishlist::isWishlisted($uid, $p->id) : false;
?>
<div class="shop-card">
    <div class="shop-card-img" style="position:relative">
        <?php if ($img): ?>
            <img data-src="<?= Html::encode($img) ?>" alt="<?= Html::encode($p->name) ?>">
        <?php else: ?>
            <i class="fa fa-image" style="font-size:48px;color:#ddd"></i>
        <?php endif; ?>
        <?php if ($onSale): ?>
            <span class="shop-sale-badge">
                <?= round((1 - $p->sale_price / $p->price) * 100) ?>% OFF
            </span>
        <?php endif; ?>
    </div>
    <div class="shop-card-body">
        <h5><a href="<?= Url::to(['/shop/store/view', 'id' => $p->id]) ?>"><?= Html::encode($p->name) ?></a></h5>
        <div class="shop-price">
            <?php if ($onSale): ?>
                <s style="color:#999;font-size:13px;font-weight:400">₱<?= number_format((float)$p->price, 2) ?></s>
                <span style="color:#e74c3c">₱<?= number_format((float)$p->sale_price, 2) ?></span>
            <?php else: ?>
                ₱<?= number_format((float)$p->price, 2) ?>
            <?php endif; ?>
        </div>
        <?php if ($saleEnds && $saleEnds > time()): ?>
            <div class="shop-countdown" data-expires="<?= $saleEnds ?>">
                <i class="fa fa-clock-o"></i> <span class="shop-timer-text"></span>
            </div>
        <?php endif; ?>
        <?php if ($p->category): ?>
            <span class="shop-meta"><i class="fa fa-tag"></i> <?= Html::encode($p->category->name) ?></span>
        <?php endif; ?>
        <?php if ($p->location): ?>
            <span class="shop-meta"><i class="fa fa-map-marker"></i> <?= Html::encode($p->location) ?></span>
        <?php endif; ?>
        <?php if ($p->vendor): ?>
            <span class="shop-meta"><i class="fa fa-store"></i> <a href="<?= Url::to(['/shop/store/vendor-store', 'id' => $p->vendor_id]) ?>"><?= Html::encode($p->vendor->shop_name) ?></a></span>
        <?php endif; ?>
    </div>
    <div class="shop-card-actions">
        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->isAdmin()): ?>
            <span class="text-muted" style="font-size:11px"><i class="fa fa-lock"></i> Admin</span>
        <?php elseif ($p->isInStock()): ?>
            <a href="<?= Url::to(['/shop/store/buy', 'id' => $p->id]) ?>" class="btn btn-success btn-xs"><i class="fa fa-shopping-cart"></i> Buy</a>
        <?php else: ?>
            <span class="label label-danger">Sold Out</span>
        <?php endif; ?>
        <?php if ($uid && !Yii::$app->user->isAdmin()): ?>
            <a href="#" class="shop-wishlist-btn <?= $wishlisted ? 'active' : '' ?>" data-url="<?= Url::to(['/shop/store/toggle-wishlist', 'productId' => $p->id]) ?>">
                <i class="fa fa-heart<?= $wishlisted ? '' : '-o' ?>"></i>
            </a>
        <?php endif; ?>
    </div>
</div>
