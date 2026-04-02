<?php

namespace humhub\modules\shop\notifications;

use humhub\modules\notification\components\BaseNotification;
use Yii;
use yii\bootstrap\Html;

class VendorRejected extends BaseNotification
{
    public $moduleId = 'shop';
    public $viewName = 'vendorRejected';

    public function getMailSubject()
    {
        return Yii::t('ShopModule.base', 'Your shop application has been rejected');
    }

    public function html()
    {
        $reason = $this->source->rejection_reason;
        $msg = Yii::t('ShopModule.base', 'Your shop application "{shopName}" has been rejected.', [
            'shopName' => Html::tag('strong', Html::encode($this->source->shop_name)),
        ]);
        if ($reason) {
            $msg .= ' ' . Yii::t('ShopModule.base', 'Reason: {reason}', ['reason' => Html::encode($reason)]);
        }
        return $msg;
    }

    public function getUrl()
    {
        return \yii\helpers\Url::to(['/shop/vendor/status']);
    }
}
