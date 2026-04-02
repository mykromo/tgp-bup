<?php
use humhub\libs\Html;
$cc = $contentContainer;
$this->title = Yii::t('ShopModule.base', 'Order Confirmed');
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><i class="fa fa-check-circle"></i> <?= Yii::t('ShopModule.base', 'Order Confirmed') ?></strong></div>
<div class="panel-body text-center" style="padding:30px">
    <i class="fa fa-check-circle text-success" style="font-size:64px"></i>
    <h3><?= Yii::t('ShopModule.base', 'Thank you for your order!') ?></h3>
    <p class="text-muted"><?= Yii::t('ShopModule.base', 'Your order has been submitted and is pending verification.') ?></p>
    <div class="well" style="display:inline-block;text-align:left">
        <p><strong><?= Yii::t('ShopModule.base', 'Order Number:') ?></strong> <?= Html::encode($order->order_number) ?></p>
        <p><strong><?= Yii::t('ShopModule.base', 'Total:') ?></strong> <?= $order->formatTotal() ?></p>
        <p><strong><?= Yii::t('ShopModule.base', 'Payment Reference:') ?></strong> <?= Html::encode($order->payment_reference) ?></p>
        <p><strong><?= Yii::t('ShopModule.base', 'Payment Method:') ?></strong> <?= Html::encode($order->payment_method) ?></p>
        <p><strong><?= Yii::t('ShopModule.base', 'Status:') ?></strong> <span class="label label-info"><?= Yii::t('ShopModule.base', 'Awaiting Verification') ?></span></p>
    </div>
    <br><br>
    <a href="<?= $cc->createUrl('/shop/store/index') ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?= Yii::t('ShopModule.base', 'Back to Shop') ?></a>
    <a href="<?= $cc->createUrl('/shop/store/my-orders') ?>" class="btn btn-primary"><i class="fa fa-list"></i> <?= Yii::t('ShopModule.base', 'My Orders') ?></a>
</div></div>
