<?php

namespace humhub\modules\stewardship;

use humhub\modules\ui\menu\MenuLink;
use Yii;
use yii\base\BaseObject;
use yii\base\Event;

class Events extends BaseObject
{
    public static function onSpaceMenuInit(Event $event)
    {
        $space = $event->sender->space;

        if ($space->isModuleEnabled('stewardship')) {
            $event->sender->addEntry(new MenuLink([
                'label' => Yii::t('StewardshipModule.base', 'Finances'),
                'url' => $space->createUrl('/stewardship/dashboard/index'),
                'icon' => 'book',
                'sortOrder' => 600,
                'isActive' => MenuLink::isActiveState('stewardship'),
            ]));
        }
    }
}
