<?php
use humhub\libs\Html;
use yii\helpers\Url;
/* @var $vendor \humhub\modules\shop\models\Vendor */
/* @var $activeTab string */
/* @var $isFavorited bool */
$coverUrl = $vendor->cover_path ? Yii::getAlias('@web') . '/' . $vendor->cover_path : '';
$logoUrl = $vendor->logo_path ? Yii::getAlias('@web') . '/' . $vendor->logo_path : '';
$isOwner = !Yii::$app->user->isGuest && $vendor->user_id === Yii::$app->user->id;
$productCount = (int) $vendor->getProducts()->where(['is_active' => 1])->count();
?>

<!-- Profile Header (matches HumHub Space/Chapter layout) -->
<div class="panel panel-default panel-profile">

    <div class="panel-profile-header">
        <!-- Banner / Cover Image -->
        <div class="store-banner-container">
            <?php if ($coverUrl): ?>
                <img src="<?= Html::encode($coverUrl) ?>" class="img-profile-header-background" alt="">
            <?php else: ?>
                <div class="store-banner-placeholder">
                    <i class="fa fa-camera"></i>
                </div>
            <?php endif; ?>

            <!-- Name overlay on banner -->
            <div class="img-profile-data">
                <h1><?= Html::encode($vendor->shop_name) ?></h1>
                <?php if ($vendor->tagline): ?>
                    <h2><?= Html::encode($vendor->tagline) ?></h2>
                <?php endif; ?>
            </div>
        </div>

        <!-- Profile Image / Logo -->
        <div class="store-profile-image-container">
            <?php if ($logoUrl): ?>
                <img src="<?= Html::encode($logoUrl) ?>" class="img-rounded profile-user-photo" alt="<?= Html::encode($vendor->shop_name) ?>">
            <?php else: ?>
                <div class="store-profile-image-placeholder">
                    <i class="fa fa-shopping-bag"></i>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Controls bar (matches Space panel-profile-controls) -->
    <div class="panel-body">
        <div class="panel-profile-controls">
            <div class="row">
                <div class="col-md-12">
                    <!-- Counters (like Space member/follower counts) -->
                    <div class="header-counter-set">
                        <span class="header-counter">
                            <strong><?= $productCount ?></strong>
                            <small>Products</small>
                        </span>
                        <?php if ($vendor->user): ?>
                        <span class="header-counter">
                            <a href="<?= $vendor->user->getUrl() ?>">
                                <strong><?= Html::encode($vendor->user->displayName) ?></strong>
                                <small>Owner</small>
                            </a>
                        </span>
                        <?php endif; ?>
                        <?php if ($vendor->location): ?>
                        <span class="header-counter">
                            <strong><i class="fa fa-map-marker"></i></strong>
                            <small><?= Html::encode($vendor->location) ?></small>
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Action buttons (like Space Join/Follow/Invite) -->
                    <div class="controls controls-header pull-right">
                        <?php if (!Yii::$app->user->isGuest && !$isOwner && !Yii::$app->user->isAdmin()): ?>
                            <a href="<?= Url::to(['/shop/store/toggle-favorite', 'vendorId' => $vendor->id]) ?>"
                               class="btn btn-<?= $isFavorited ? 'primary active' : 'primary' ?> btn-sm" data-method="post">
                                <i class="fa fa-star<?= $isFavorited ? '' : '-o' ?>"></i> <?= $isFavorited ? 'Favorited' : 'Favorite' ?>
                            </a>
                        <?php endif; ?>
                        <?php if ($isOwner): ?>
                            <a href="<?= Url::to(['/shop/seller/edit-store']) ?>" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> Edit Store</a>
                            <a href="<?= Url::to(['/shop/seller/dashboard']) ?>" class="btn btn-default btn-sm"><i class="fa fa-cog"></i> Manage</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($vendor->status === \humhub\modules\shop\models\Vendor::STATUS_SUSPENDED): ?>
    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> This store is currently suspended.</div>
<?php endif; ?>

<?php if (!Yii::$app->user->isGuest && Yii::$app->user->isAdmin()): ?>
    <div class="alert alert-info" style="display:flex;align-items:center;justify-content:space-between">
        <span><i class="fa fa-shield"></i> You are viewing this store as an administrator for investigation purposes.</span>
        <a href="<?= Url::to(['/shop/admin/stores']) ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Back to Admin</a>
    </div>
<?php endif; ?>
