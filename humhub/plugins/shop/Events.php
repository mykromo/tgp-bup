<?php

namespace humhub\modules\shop;

use humhub\modules\ui\menu\MenuLink;
use Yii;
use yii\base\BaseObject;
use yii\base\Event;

class Events extends BaseObject
{
    public static function onTopMenuInit(Event $event)
    {
        $module = Yii::$app->getModule('shop');
        if ($module === null) {
            return;
        }

        $event->sender->addEntry(new MenuLink([
            'label' => Yii::t('ShopModule.base', 'Shop'),
            'url' => ['/shop/store/index'],
            'icon' => 'shopping-cart',
            'sortOrder' => 500,
            'isActive' => (Yii::$app->controller && Yii::$app->controller->module && Yii::$app->controller->module->id === 'shop'),
        ]));
    }
}
