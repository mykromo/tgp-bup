<?php

namespace humhub\modules\shop\notifications;

use humhub\modules\notification\components\BaseNotification;
use Yii;
use yii\bootstrap\Html;

class VendorApproved extends BaseNotification
{
    public $moduleId = 'shop';
    public $viewName = 'vendorApproved';

    public function getMailSubject()
    {
        return Yii::t('ShopModule.base', 'Your shop application has been approved!');
    }

    public function html()
    {
        return Yii::t('ShopModule.base', 'Your shop application "{shopName}" has been approved! You can now start adding products.', [
            'shopName' => Html::tag('strong', Html::encode($this->source->shop_name)),
        ]);
    }

    public function getUrl()
    {
        return \yii\helpers\Url::to(['/shop/vendor/status']);
    }
}
