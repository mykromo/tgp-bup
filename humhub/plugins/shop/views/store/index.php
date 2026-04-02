<?php
use humhub\libs\Html;
use yii\helpers\Url;
use humhub\modules\shop\models\Wishlist;
humhub\modules\shop\assets\ShopAsset::register($this);
$this->title = Yii::t('ShopModule.base', 'Shop');
$uid = Yii::$app->user->isGuest ? 0 : Yii::$app->user->id;
?>
<div class="panel panel-default">
<div class="panel-heading">
    <strong><i class="fa fa-shopping-cart"></i> <?= Yii::t('ShopModule.base', 'Shop') ?></strong>
    <div class="pull-right">
        <?php if (!Yii::$app->user->isGuest): ?>
        <a href="<?= Url::to(['/shop/store/my-orders']) ?>" class="btn btn-default btn-sm"><i class="fa fa-list"></i> My Orders</a>
        <a href="<?= Url::to(['/shop/address/index']) ?>" class="btn btn-default btn-sm"><i class="fa fa-map-marker"></i> Addresses</a>
            <?php if ($isVendor && $isVendor->isApproved()): ?>
                <a href="<?= Url::to(['/shop/seller/dashboard']) ?>" class="btn btn-default btn-sm"><i class="fa fa-cube"></i> My Shop</a>
            <?php elseif (!$isVendor): ?>
                <a href="<?= Url::to(['/shop/vendor/apply']) ?>" class="btn btn-success btn-sm"><i class="fa fa-store"></i> Become a Seller</a>
            <?php elseif ($isVendor->isPending()): ?>
                <a href="<?= Url::to(['/shop/vendor/status']) ?>" class="btn btn-info btn-sm"><i class="fa fa-clock-o"></i> Pending</a>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($canManage): ?>
        <a href="<?= Url::to(['/shop/vendor/applications']) ?>" class="btn btn-default btn-sm"><i class="fa fa-users"></i> Applications</a>
        <a href="<?= Url::to(['/shop/admin/products']) ?>" class="btn btn-primary btn-sm"><i class="fa fa-cog"></i> Manage</a>
        <?php endif; ?>
    </div>
</div>
<div class="panel-body">

<!-- Search & Filter -->
<div class="shop-search">
    <form method="get" action="<?= Url::to(['/shop/store/index']) ?>" style="display:contents">
        <input type="text" name="q" value="<?= Html::encode($keyword ?? '') ?>" class="form-control input-sm" placeholder="Search products..." style="width:200px">
        <select name="category" class="form-control input-sm">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cid => $cname): ?>
                <option value="<?= $cid ?>" <?= ($selectedCategory ?? '') == $cid ? 'selected' : '' ?>><?= Html::encode($cname) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="location" value="<?= Html::encode($selectedLocation ?? '') ?>" class="form-control input-sm" placeholder="Location..." style="width:140px">
        <button type="submit" class="btn btn-default btn-sm"><i class="fa fa-search"></i> Search</button>
    </form>
    <?php if (!Yii::$app->user->isGuest): ?>
    <a href="<?= Url::to(['/shop/store/wishlist']) ?>" class="btn btn-default btn-sm"><i class="fa fa-heart"></i> Wishlist</a>
    <a href="<?= Url::to(['/shop/store/favorites']) ?>" class="btn btn-default btn-sm"><i class="fa fa-star"></i> Favorites</a>
    <?php endif; ?>
</div>

<?php if (empty($products)): ?>
    <div class="text-center text-muted" style="padding:40px">
        <i class="fa fa-shopping-cart" style="font-size:48px;color:#ddd"></i>
        <p style="margin-top:10px">No products found.</p>
    </div>
<?php else: ?>
    <div class="shop-grid">
    <?php foreach ($products as $p):
        $img = $p->getFirstImageUrl();
        $wishlisted = $uid ? Wishlist::isWishlisted($uid, $p->id) : false;
    ?>
        <div class="shop-card">
            <div class="shop-card-img" style="position:relative">
                <?php if ($img): ?>
                    <img data-src="<?= Html::encode($img) ?>" alt="<?= Html::encode($p->name) ?>">
                <?php else: ?>
                    <i class="fa fa-image" style="font-size:48px;color:#ddd"></i>
                <?php endif; ?>
                <?php if ($p->isOnSale()): ?>
                    <span class="shop-sale-badge">SALE</span>
                <?php endif; ?>
            </div>
            <div class="shop-card-body">
                <h5><a href="<?= Url::to(['/shop/store/view', 'id' => $p->id]) ?>"><?= Html::encode($p->name) ?></a></h5>
                <div class="shop-price"><?= $p->formatPrice() ?></div>
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
                <?php if ($p->isInStock()): ?>
                    <a href="<?= Url::to(['/shop/store/buy', 'id' => $p->id]) ?>" class="btn btn-success btn-xs"><i class="fa fa-shopping-cart"></i> Buy</a>
                <?php else: ?>
                    <span class="label label-danger">Sold Out</span>
                <?php endif; ?>
                <?php if ($uid): ?>
                    <a href="#" class="shop-wishlist-btn <?= $wishlisted ? 'active' : '' ?>" data-url="<?= Url::to(['/shop/store/toggle-wishlist', 'productId' => $p->id]) ?>">
                        <i class="fa fa-heart<?= $wishlisted ? '' : '-o' ?>"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
    <?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
<?php endif; ?>

</div></div>
