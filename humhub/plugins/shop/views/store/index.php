<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'Shop');
?>
<div class="panel panel-default">
<div class="panel-heading">
    <strong><i class="fa fa-shopping-cart"></i> <?= Yii::t('ShopModule.base', 'Shop') ?></strong>
    <div class="pull-right">
        <?php if (!Yii::$app->user->isGuest): ?>
        <a href="<?= Url::to(['/shop/store/my-orders']) ?>" class="btn btn-default btn-sm"><i class="fa fa-list"></i> <?= Yii::t('ShopModule.base', 'My Orders') ?></a>
            <?php if ($isVendor && $isVendor->isApproved()): ?>
                <a href="<?= Url::to(['/shop/admin/products']) ?>" class="btn btn-default btn-sm"><i class="fa fa-cube"></i> <?= Yii::t('ShopModule.base', 'My Products') ?></a>
            <?php elseif (!$isVendor): ?>
                <a href="<?= Url::to(['/shop/vendor/apply']) ?>" class="btn btn-success btn-sm"><i class="fa fa-store"></i> <?= Yii::t('ShopModule.base', 'Become a Seller') ?></a>
            <?php elseif ($isVendor->isPending()): ?>
                <a href="<?= Url::to(['/shop/vendor/status']) ?>" class="btn btn-info btn-sm"><i class="fa fa-clock-o"></i> <?= Yii::t('ShopModule.base', 'Application Pending') ?></a>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($canManage): ?>
        <a href="<?= Url::to(['/shop/vendor/applications']) ?>" class="btn btn-default btn-sm"><i class="fa fa-users"></i> <?= Yii::t('ShopModule.base', 'Applications') ?></a>
        <a href="<?= Url::to(['/shop/admin/products']) ?>" class="btn btn-primary btn-sm"><i class="fa fa-cog"></i> <?= Yii::t('ShopModule.base', 'Manage') ?></a>
        <?php endif; ?>
    </div>
</div>
<div class="panel-body">
<?php if (empty($products)): ?>
    <div class="text-center text-muted" style="padding:30px"><i class="fa fa-shopping-cart" style="font-size:48px"></i><p>No products available.</p></div>
<?php else: ?>
    <div class="row">
    <?php foreach ($products as $p): ?>
        <div class="col-sm-6 col-md-4" style="margin-bottom:20px">
            <div class="panel panel-default" style="min-height:200px">
                <?php $firstImg = $p->getFirstImageUrl(); ?>
                <?php if ($firstImg): ?>
                <div style="height:140px;overflow:hidden;background:#f5f5f5;text-align:center">
                    <img src="<?= Html::encode($firstImg) ?>" style="max-height:140px;max-width:100%">
                </div>
                <?php endif; ?>
                <div class="panel-body">
                    <h4 style="margin:5px 0"><?= Html::encode($p->name) ?></h4>
                    <p class="text-muted" style="font-size:12px"><?= Html::encode(mb_substr($p->description ?? '', 0, 80)) ?></p>
                    <h4 style="color:#337ab7"><?= $p->formatPrice() ?></h4>
                    <?php if (!$p->isInStock()): ?>
                        <span class="label label-danger">Out of Stock</span>
                    <?php else: ?>
                        <a href="<?= Url::to(['/shop/store/buy', 'id' => $p->id]) ?>" class="btn btn-success btn-sm btn-block">
                            <i class="fa fa-shopping-cart"></i> <?= Yii::t('ShopModule.base', 'Buy Now') ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
    <?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
<?php endif; ?>
</div></div>
