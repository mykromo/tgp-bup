<?php

namespace humhub\modules\shop;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\space\models\Space;
use Yii;

class Module extends ContentContainerModule
{
    public $controllerNamespace = 'humhub\modules\shop\controllers';

    public function init()
    {
        parent::init();
        Yii::setAlias('@shop', $this->getBasePath());
    }

    public function getContentContainerTypes()
    {
        return [Space::class];
    }

    public function getPermissions($contentContainer = null)
    {
        if ($contentContainer !== null) {
            return [new permissions\ManageShop()];
        }
        return [];
    }

    public function getName()
    {
        return Yii::t('ShopModule.base', 'Chapter Shop');
    }

    public function getDescription()
    {
        return Yii::t('ShopModule.base', 'Sell products with manual payment and reference number tracking');
    }
}
