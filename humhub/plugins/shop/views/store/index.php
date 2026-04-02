<?php
use humhub\libs\Html;
use yii\helpers\Url;
humhub\modules\shop\assets\ShopAsset::register($this);
$this->title = Yii::t('ShopModule.base', 'Shop');
?>
<div class="panel panel-default">
<div class="panel-heading">
    <strong><i class="fa fa-shopping-cart"></i> <?= Yii::t('ShopModule.base', 'Shop') ?></strong>
    <div class="pull-right">
        <?php if (!Yii::$app->user->isGuest): ?>
            <?php if (!Yii::$app->user->isAdmin()): ?>
                <a href="<?= Url::to(['/shop/store/my-orders']) ?>" class="btn btn-default btn-sm"><i class="fa fa-list"></i> My Orders</a>
                <a href="<?= Url::to(['/shop/address/index']) ?>" class="btn btn-default btn-sm"><i class="fa fa-map-marker"></i> Addresses</a>
                <a href="<?= Url::to(['/shop/store/wishlist']) ?>" class="btn btn-default btn-sm"><i class="fa fa-heart"></i> Wishlist</a>
                <a href="<?= Url::to(['/shop/store/favorites']) ?>" class="btn btn-default btn-sm"><i class="fa fa-star"></i> Favorites</a>
                <?php if ($isVendor && $isVendor->isApproved()): ?>
                    <a href="<?= Url::to(['/shop/seller/dashboard']) ?>" class="btn btn-default btn-sm"><i class="fa fa-cube"></i> My Shop</a>
                <?php elseif ($isVendor && $isVendor->status === 'suspended'): ?>
                    <a href="<?= Url::to(['/shop/vendor/request-reenable']) ?>" class="btn btn-warning btn-sm"><i class="fa fa-unlock"></i> Disabled</a>
                <?php elseif (!$isVendor): ?>
                    <a href="<?= Url::to(['/shop/vendor/apply']) ?>" class="btn btn-success btn-sm"><i class="fa fa-store"></i> Become a Seller</a>
                <?php elseif ($isVendor->isPending()): ?>
                    <a href="<?= Url::to(['/shop/vendor/status']) ?>" class="btn btn-info btn-sm"><i class="fa fa-clock-o"></i> Pending</a>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($canManage): ?>
            <a href="<?= Url::to(['/shop/admin/products']) ?>" class="btn btn-primary btn-sm"><i class="fa fa-cog"></i> Manage</a>
        <?php endif; ?>
    </div>
</div>
<div class="panel-body">

<form method="get" action="<?= Url::to(['/shop/store/index']) ?>" class="form-inline" style="margin-bottom:16px">
    <input type="text" name="q" value="<?= Html::encode($keyword ?? '') ?>" class="form-control input-sm" placeholder="Search products..." style="width:180px">
    <select name="category" class="form-control input-sm">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cid => $cname): ?>
            <option value="<?= $cid ?>" <?= ($selectedCategory ?? '') == $cid ? 'selected' : '' ?>><?= Html::encode($cname) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="text" name="location" value="<?= Html::encode($selectedLocation ?? '') ?>" class="form-control input-sm" placeholder="Location..." style="width:130px">
    <button type="submit" class="btn btn-default btn-sm"><i class="fa fa-search"></i></button>
</form>

<?php if (empty($products)): ?>
    <div class="text-center text-muted" style="padding:40px">
        <i class="fa fa-shopping-cart" style="font-size:48px;color:#ddd"></i>
        <p style="margin-top:10px">No products found.</p>
    </div>
<?php else: ?>
    <div class="shop-grid">
    <?php foreach ($products as $p): ?>
        <?= $this->render('_product_card', ['p' => $p]) ?>
    <?php endforeach; ?>
    </div>
    <?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
<?php endif; ?>

</div></div>
