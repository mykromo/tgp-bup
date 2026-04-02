<?php
use yii\helpers\Html;
?>
<?= Yii::t('ShopModule.base', 'Your shop application "{shopName}" has been rejected.', [
    'shopName' => Html::tag('strong', Html::encode($source->shop_name)),
]) ?>
<?php if ($source->rejection_reason): ?>
<br><?= Yii::t('ShopModule.base', 'Reason: {reason}', ['reason' => Html::encode($source->rejection_reason)]) ?>
<?php endif; ?>
