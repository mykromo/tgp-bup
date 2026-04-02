<?php
use yii\helpers\Html;
?>
<?= Yii::t('ShopModule.base', 'Your shop application "{shopName}" has been approved! You can now start adding products and selling on the platform.', [
    'shopName' => Html::tag('strong', Html::encode($source->shop_name)),
]) ?>
