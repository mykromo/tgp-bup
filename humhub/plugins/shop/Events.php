<?php

namespace humhub\modules\shop;

use humhub\modules\admin\widgets\AdminMenu;
use humhub\modules\ui\menu\MenuLink;
use Yii;
use yii\base\BaseObject;
use yii\base\Event;

class Events extends BaseObject
{
    /**
     * Add Shop to top menu — only for non-admin users.
     */
    public static function onTopMenuInit(Event $event)
    {
        $module = Yii::$app->getModule('shop');
        if ($module === null) {
            return;
        }

        // Administrators should not see the Shop in the main navigation
        if (!Yii::$app->user->isGuest && Yii::$app->user->isAdmin()) {
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

    /**
     * Add Shop Management to the Administration menu.
     */
    public static function onAdminMenuInit(Event $event)
    {
        $event->sender->addEntry(new MenuLink([
            'id' => 'shop',
            'label' => Yii::t('ShopModule.base', 'Shop'),
            'url' => ['/shop/admin/index'],
            'icon' => 'shopping-cart',
            'sortOrder' => 650,
            'isActive' => MenuLink::isActiveState('shop', 'admin'),
            'isVisible' => Yii::$app->user->isAdmin(),
        ]));
    }
}
