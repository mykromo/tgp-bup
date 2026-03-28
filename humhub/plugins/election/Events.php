<?php

namespace humhub\modules\election;

use humhub\modules\ui\menu\MenuLink;
use Yii;
use yii\base\BaseObject;
use yii\base\Event;

class Events extends BaseObject
{
    public static function onSpaceMenuInit(Event $event)
    {
        $space = $event->sender->space;

        if ($space->isModuleEnabled('election')) {
            $event->sender->addEntry(new MenuLink([
                'label' => Yii::t('ElectionModule.base', 'Elections'),
                'url' => $space->createUrl('/election/election/index'),
                'icon' => 'check-square-o',
                'sortOrder' => 500,
                'isActive' => MenuLink::isActiveState('election'),
            ]));
        }
    }
}
