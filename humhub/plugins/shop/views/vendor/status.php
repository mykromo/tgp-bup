<?php
use humhub\libs\Html;
use humhub\modules\shop\models\Vendor;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'Vendor Status');
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><?= $this->title ?></strong></div>
<div class="panel-body">
    <div class="well">
        <p><strong><?= Yii::t('ShopModule.base', 'Shop Name:') ?></strong> <?= Html::encode($vendor->shop_name) ?></p>
        <p><strong><?= Yii::t('ShopModule.base', 'Status:') ?></strong>
            <span class="label label-<?= Vendor::getStatusBadge($vendor->status) ?>"><?= Vendor::getStatusLabels()[$vendor->status] ?? $vendor->status ?></span></p>

        <?php if ($vendor->status === Vendor::STATUS_REJECTED && $vendor->rejection_reason): ?>
            <p><strong><?= Yii::t('ShopModule.base', 'Reason:') ?></strong> <span class="text-danger"><?= Html::encode($vendor->rejection_reason) ?></span></p>
        <?php endif; ?>

        <?php if ($vendor->status === Vendor::STATUS_SUSPENDED): ?>
            <?php if ($vendor->disabled_reason): ?>
                <p><strong><?= Yii::t('ShopModule.base', 'Disabled Reason:') ?></strong> <span class="text-danger"><?= Html::encode($vendor->disabled_reason) ?></span></p>
            <?php endif; ?>
            <?php if ($vendor->disabled_at): ?>
                <p><strong><?= Yii::t('ShopModule.base', 'Disabled On:') ?></strong> <?= Yii::$app->formatter->asDatetime($vendor->disabled_at) ?></p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($vendor->reviewed_at): ?>
            <p><strong><?= Yii::t('ShopModule.base', 'Reviewed:') ?></strong> <?= Yii::$app->formatter->asDatetime($vendor->reviewed_at) ?></p>
        <?php endif; ?>
    </div>

    <?php if ($vendor->isApproved()): ?>
        <a href="<?= Url::to(['/shop/seller/dashboard']) ?>" class="btn btn-primary"><i class="fa fa-cube"></i> <?= Yii::t('ShopModule.base', 'Go to My Shop') ?></a>
    <?php elseif ($vendor->status === Vendor::STATUS_REJECTED): ?>
        <a href="<?= Url::to(['/shop/vendor/apply']) ?>" class="btn btn-primary"><i class="fa fa-refresh"></i> <?= Yii::t('ShopModule.base', 'Reapply') ?></a>
    <?php elseif ($vendor->status === Vendor::STATUS_SUSPENDED): ?>
        <a href="<?= Url::to(['/shop/vendor/request-reenable']) ?>" class="btn btn-warning"><i class="fa fa-unlock"></i> <?= Yii::t('ShopModule.base', 'Request Re-enablement') ?></a>
    <?php endif; ?>
    <a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?= Yii::t('ShopModule.base', 'Back to Shop') ?></a>
</div></div>
