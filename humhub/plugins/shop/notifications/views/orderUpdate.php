<?php
use yii\helpers\Html;
echo Html::encode($viewable->customMessage ?? 'Your order has been updated.');
if ($source) {
    echo ' Order: ' . Html::encode($source->order_number);
}
