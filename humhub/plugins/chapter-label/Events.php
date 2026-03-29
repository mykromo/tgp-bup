<?php

namespace humhub\modules\chapterlabel;

use Yii;
use yii\base\BaseObject;
use yii\base\Event;

class Events extends BaseObject
{
    public static function onInit(Event $event)
    {
        $module = Yii::$app->getModule('chapter-label');
        if ($module === null) {
            return;
        }

        // Point message overwrites to this module's messages directory
        Yii::$app->i18n->messageOverwritePath = $module->getBasePath() . '/messages';
    }
}
