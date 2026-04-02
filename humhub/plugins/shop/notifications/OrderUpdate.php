<?php

namespace humhub\modules\shop\notifications;

use humhub\modules\notification\components\BaseNotification;
use Yii;
use yii\helpers\Html;

class OrderUpdate extends BaseNotification
{
    public $moduleId = 'shop';
    public $viewName = 'orderUpdate';
    public $customMessage = '';

    public function getMailSubject()
    {
        return $this->customMessage ?: Yii::t('ShopModule.base', 'Order Update');
    }

    public function html()
    {
        $orderNum = $this->source ? $this->source->order_number : '';
        return Html::encode($this->customMessage ?: 'Order ' . $orderNum . ' has been updated.');
    }

    public function getUrl()
    {
        if ($this->source) {
            return \yii\helpers\Url::to(['/shop/store/my-orders']);
        }
        return null;
    }
}
