<?php
use humhub\libs\Html;
use yii\helpers\Url;
/* @var $vendor \humhub\modules\shop\models\Vendor */
/* @var $activeTab string */
/* @var $isFollowing bool */
$coverUrl = $vendor->cover_path ? Yii::getAlias('@web') . '/' . $vendor->cover_path : '';
$logoUrl = $vendor->logo_path ? Yii::getAlias('@web') . '/' . $vendor->logo_path : '';
$isOwner = !Yii::$app->user->isGuest && $vendor->user_id === Yii::$app->user->id;
$isAdmin = !Yii::$app->user->isGuest && Yii::$app->user->isAdmin();
$productCount = $vendor->getActiveProductCount();
$followerCount = $vendor->getFollowerCount();
?>

<div class="panel panel-default panel-profile">

    <div class="panel-profile-header">
        <!-- Cover / Banner -->
        <div class="image-upload-container profile-banner-image-container">
            <?php if ($coverUrl): ?>
                <img src="<?= Html::encode($coverUrl) ?>" class="img-profile-header-background" style="width:100%" alt="">
            <?php else: ?>
                <div class="store-banner-placeholder"></div>
            <?php endif; ?>

            <!-- Name + tagline overlay on cover, right of logo -->
            <div class="img-profile-data">
                <h1 class="space"><?= Html::encode($vendor->shop_name) ?></h1>
                <?php if ($vendor->tagline): ?>
                    <h2 class="space"><?= Html::encode($vendor->tagline) ?></h2>
                <?php endif; ?>
            </div>

            <?php if ($isOwner): ?>
            <div class="image-upload-buttons">
                <?= Html::beginForm(Url::to(['/shop/seller/upload-cover']), 'post', ['enctype' => 'multipart/form-data', 'style' => 'display:inline']) ?>
                    <label class="btn btn-info btn-sm profile-image-upload" style="margin:0;cursor:pointer" title="Upload cover">
                        <i class="fa fa-upload"></i>
                        <input type="file" name="cover" accept=".jpg,.jpeg,.png,.webp" style="display:none" onchange="this.form.submit()">
                    </label>
                <?= Html::endForm() ?>
                <?php if ($coverUrl): ?>
                    <a href="<?= Url::to(['/shop/seller/delete-cover']) ?>" class="btn btn-danger btn-sm" data-method="post"
                       data-confirm="Delete cover photo?"><i class="fa fa-remove"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Profile Image / Logo -->
        <div class="image-upload-container profile-user-photo-container" style="width:140px;height:140px;">
            <?php if ($logoUrl): ?>
                <img src="<?= Html::encode($logoUrl) ?>" class="img-profile-header-background profile-user-photo"
                     style="width:130px;height:130px;border-radius:3px;object-fit:cover" alt="">
            <?php else: ?>
                <div class="store-profile-image-placeholder">
                    <i class="fa fa-shopping-bag"></i>
                </div>
            <?php endif; ?>

            <?php if ($isOwner): ?>
            <div class="image-upload-buttons">
                <?= Html::beginForm(Url::to(['/shop/seller/upload-logo']), 'post', ['enctype' => 'multipart/form-data', 'style' => 'display:inline']) ?>
                    <label class="btn btn-info btn-sm profile-image-upload" style="margin:0;cursor:pointer" title="Upload logo">
                        <i class="fa fa-upload"></i>
                        <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp" style="display:none" onchange="this.form.submit()">
                    </label>
                <?= Html::endForm() ?>
                <?php if ($logoUrl): ?>
                    <a href="<?= Url::to(['/shop/seller/delete-logo']) ?>" class="btn btn-danger btn-sm" data-method="post"
                       data-confirm="Delete logo?"><i class="fa fa-remove"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Controls bar — matches Space profileHeaderControls exactly -->
    <div class="panel-body">
        <div class="panel-profile-controls">
            <div class="row">
                <div class="col-md-12">
                    <!-- Counters — uses HumHub native .statistics layout -->
                    <div class="statistics pull-left">
                        <div class="pull-left entry">
                            <span class="count"><?= $productCount ?></span><br>
                            <span class="title">Products</span>
                        </div>
                        <div class="pull-left entry">
                            <span class="count"><?= $followerCount ?></span><br>
                            <span class="title">Followers</span>
                        </div>
                    </div>

                    <!-- Action buttons — matches Space controls-header -->
                    <div class="controls controls-header pull-right">
                        <?php if (!Yii::$app->user->isGuest && !$isOwner && !$isAdmin): ?>
                            <a href="<?= Url::to(['/shop/store/toggle-follow', 'vendorId' => $vendor->id]) ?>"
                               class="btn btn-<?= $isFollowing ? 'primary active' : 'primary' ?>" data-method="post">
                                <i class="fa fa-<?= $isFollowing ? 'check' : 'plus' ?>"></i> <?= $isFollowing ? 'Following' : 'Follow' ?>
                            </a>
                        <?php endif; ?>
                        <?php if ($isOwner): ?>
                            <a href="<?= Url::to(['/shop/seller/edit-store']) ?>" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> Edit Store</a>
                            <a href="<?= Url::to(['/shop/seller/dashboard']) ?>" class="btn btn-default btn-sm"><i class="fa fa-cog"></i></a>
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

<?php if ($isAdmin): ?>
    <div class="alert alert-info" style="display:flex;align-items:center;justify-content:space-between">
        <span><i class="fa fa-shield"></i> Viewing as administrator for investigation.</span>
        <a href="<?= Url::to(['/shop/admin/stores']) ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Back to Admin</a>
    </div>
<?php endif; ?>
