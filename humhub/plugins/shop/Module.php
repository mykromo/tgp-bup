<?php

namespace humhub\modules\shop;

use humhub\components\Module as BaseModule;
use Yii;

class Module extends BaseModule
{
    public $controllerNamespace = 'humhub\modules\shop\controllers';

    public function init()
    {
        parent::init();
        Yii::setAlias('@shop', $this->getBasePath());
    }

    public function getName()
    {
        return Yii::t('ShopModule.base', 'Shop');
    }

    public function getDescription()
    {
        return Yii::t('ShopModule.base', 'System-wide shop with manual payment and reference number tracking');
    }
}
