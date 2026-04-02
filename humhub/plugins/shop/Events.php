<?php

namespace humhub\modules\shop;

use humhub\modules\ui\menu\MenuLink;
use Yii;
use yii\base\BaseObject;
use yii\base\Event;

class Events extends BaseObject
{
    public static function onSpaceMenuInit(Event $event)
    {
        $space = $event->sender->space;
        if ($space->isModuleEnabled('shop')) {
            $event->sender->addEntry(new MenuLink([
                'label' => Yii::t('ShopModule.base', 'Shop'),
                'url' => $space->createUrl('/shop/store/index'),
                'icon' => 'shopping-cart',
                'sortOrder' => 700,
                'isActive' => MenuLink::isActiveState('shop'),
            ]));
        }
    }
}
