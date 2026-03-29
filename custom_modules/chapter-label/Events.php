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

        // Catch-all callback for any remaining labels not in message files
        Yii::$app->i18n->beforeTranslateCallback = [static::class, 'rewriteLabel'];
    }

    public static function rewriteLabel($category, $message, $params, $language)
    {
        if (stripos($message, 'space') === false) {
            return [$category, $message, $params, $language];
        }

        if (strpos($language, 'en') !== 0) {
            return [$category, $message, $params, $language];
        }

        static $replacements = [
            ['Spaces', 'Chapters'],
            ['spaces', 'chapters'],
            ['SPACES', 'CHAPTERS'],
            ['Space',  'Chapter'],
            ['space',  'chapter'],
            ['SPACE',  'CHAPTER'],
        ];

        $replaced = $message;
        foreach ($replacements as [$search, $replace]) {
            if (strpos($replaced, $search) !== false) {
                $replaced = str_replace($search, $replace, $replaced);
            }
        }

        return [$category, $replaced, $params, $language];
    }
}
