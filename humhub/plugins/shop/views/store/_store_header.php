<?php
use humhub\libs\Html;
use yii\helpers\Url;
/* @var $vendor \humhub\modules\shop\models\Vendor */
/* @var $activeTab string */
/* @var $isFavorited bool */
$coverUrl = $vendor->cover_path ? Yii::getAlias('@web') . '/' . $vendor->cover_path : '';
$logoUrl = $vendor->logo_path ? Yii::getAlias('@web') . '/' . $vendor->logo_path : '';
$isOwner = !Yii::$app->user->isGuest && $vendor->user_id === Yii::$app->user->id;
?>
<div class="store-profile">
    <div class="store-cover" <?= $coverUrl ? 'style="background-image:url(' . Html::encode($coverUrl) . ')"' : '' ?>></div>
    <div class="store-header">
        <div class="store-info">
            <?php if ($logoUrl): ?>
                <img src="<?= Html::encode($logoUrl) ?>" class="store-logo" alt="<?= Html::encode($vendor->shop_name) ?>">
            <?php else: ?>
                <div class="store-logo-placeholder"><i class="fa fa-store"></i></div>
            <?php endif; ?>
            <div class="store-info-text">
                <h2><?= Html::encode($vendor->shop_name) ?></h2>
                <?php if ($vendor->tagline): ?>
                    <p class="store-tagline"><?= Html::encode($vendor->tagline) ?></p>
                <?php endif; ?>
                <div class="store-meta-row">
                    <?php if ($vendor->location): ?>
                        <span><i class="fa fa-map-marker"></i> <?= Html::encode($vendor->location) ?></span>
                    <?php endif; ?>
                    <span><i class="fa fa-cube"></i> <?= count($vendor->products) ?> products</span>
                    <?php if ($vendor->user): ?>
                        <span><i class="fa fa-user"></i> <?= Html::encode($vendor->user->displayName) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="store-info-actions">
                <?php if (!Yii::$app->user->isGuest && !$isOwner): ?>
                    <a href="<?= Url::to(['/shop/store/toggle-favorite', 'vendorId' => $vendor->id]) ?>"
                       class="btn btn-<?= $isFavorited ? 'warning' : 'default' ?> btn-sm" data-method="post">
                        <i class="fa fa-star<?= $isFavorited ? '' : '-o' ?>"></i> <?= $isFavorited ? 'Favorited' : 'Favorite' ?>
                    </a>
                <?php endif; ?>
                <?php if ($isOwner): ?>
                    <a href="<?= Url::to(['/shop/seller/edit-store']) ?>" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> Edit Store</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($vendor->status === \humhub\modules\shop\models\Vendor::STATUS_SUSPENDED): ?>
        <div class="alert alert-warning" style="margin-top:10px;margin-bottom:0"><i class="fa fa-exclamation-triangle"></i> This store is currently suspended.</div>
    <?php endif; ?>

    <div class="store-nav">
        <ul>
            <li><a href="<?= Url::to(['/shop/store/vendor-store', 'id' => $vendor->id]) ?>" class="<?= ($activeTab ?? '') === 'all' ? 'active' : '' ?>"><i class="fa fa-th"></i> All Products</a></li>
            <li><a href="<?= Url::to(['/shop/store/vendor-store', 'id' => $vendor->id, 'tab' => 'categories']) ?>" class="<?= ($activeTab ?? '') === 'categories' ? 'active' : '' ?>"><i class="fa fa-tags"></i> Categories</a></li>
            <li><a href="<?= Url::to(['/shop/store/vendor-store', 'id' => $vendor->id, 'tab' => 'sale']) ?>" class="<?= ($activeTab ?? '') === 'sale' ? 'active' : '' ?>"><i class="fa fa-bolt"></i> On Sale</a></li>
            <li><a href="<?= Url::to(['/shop/store/vendor-store', 'id' => $vendor->id, 'tab' => 'about']) ?>" class="<?= ($activeTab ?? '') === 'about' ? 'active' : '' ?>"><i class="fa fa-info-circle"></i> About</a></li>
        </ul>
    </div>
</div>
