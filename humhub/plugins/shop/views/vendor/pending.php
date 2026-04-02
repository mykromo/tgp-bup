<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'Application Submitted');
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><?= $this->title ?></strong></div>
<div class="panel-body text-center" style="padding:40px">
    <i class="fa fa-clock-o text-info" style="font-size:64px"></i>
    <h3><?= Yii::t('ShopModule.base', 'Your application is under review') ?></h3>
    <p class="text-muted"><?= Yii::t('ShopModule.base', 'An administrator will review your documents and approve or reject your application. You will be notified once a decision is made.') ?></p>
    <div class="well" style="display:inline-block;text-align:left">
        <p><strong><?= Yii::t('ShopModule.base', 'Shop Name:') ?></strong> <?= Html::encode($vendor->shop_name) ?></p>
        <p><strong><?= Yii::t('ShopModule.base', 'Status:') ?></strong> <span class="label label-info"><?= Yii::t('ShopModule.base', 'Pending Review') ?></span></p>
        <p><strong><?= Yii::t('ShopModule.base', 'Submitted:') ?></strong> <?= Yii::$app->formatter->asDatetime($vendor->created_at) ?></p>
        <p><strong><?= Yii::t('ShopModule.base', 'Documents:') ?></strong> <?= count($vendor->documents) ?> uploaded</p>
    </div>
    <br><br>
    <a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?= Yii::t('ShopModule.base', 'Back to Shop') ?></a>
</div></div>
