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
                'label' => Yii::t('ElectionModule.base', 'Officers'),
                'url' => $space->createUrl('/election/officer/index'),
                'icon' => 'id-badge',
                'sortOrder' => 501,
                'isActive' => MenuLink::isActiveState('election'),
            ]));
        }
    }
}
