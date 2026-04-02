<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'Order Confirmed');
$receiptHtml = \humhub\modules\shop\helpers\Receipt::generateHtml($order);
?>
<div class="panel panel-default">
<div class="panel-heading">
    <strong><i class="fa fa-check-circle"></i> <?= Yii::t('ShopModule.base', 'Order Confirmed') ?></strong>
    <div class="pull-right">
        <a href="<?= Url::to(['/shop/store/download-receipt', 'id' => $order->id]) ?>" class="btn btn-default btn-sm" data-pjax-prevent><i class="fa fa-download"></i> Download Receipt</a>
    </div>
</div>
<div class="panel-body">
    <div class="text-center" style="margin-bottom:20px">
        <i class="fa fa-check-circle text-success" style="font-size:48px"></i>
        <h3 style="margin-top:10px"><?= Yii::t('ShopModule.base', 'Thank you for your order!') ?></h3>
        <p class="text-muted"><?= Yii::t('ShopModule.base', 'Your order has been submitted. A receipt has been sent to your email.') ?></p>
    </div>

    <!-- Inline receipt -->
    <div style="border:1px solid #eee;border-radius:4px;padding:10px;background:#fafafa">
        <?= $receiptHtml ?>
    </div>

    <hr>
    <a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> <?= Yii::t('ShopModule.base', 'Back to Shop') ?></a>
    <a href="<?= Url::to(['/shop/store/my-orders']) ?>" class="btn btn-primary"><i class="fa fa-list"></i> <?= Yii::t('ShopModule.base', 'My Orders') ?></a>
    <a href="<?= Url::to(['/shop/store/download-receipt', 'id' => $order->id]) ?>" class="btn btn-default pull-right" data-pjax-prevent><i class="fa fa-download"></i> Download Receipt</a>
</div></div>
