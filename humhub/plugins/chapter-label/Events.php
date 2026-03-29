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

        Yii::$app->i18n->messageOverwritePath = $module->getBasePath() . '/messages';
        Yii::$app->i18n->beforeTranslateCallback = [static::class, 'rewrite'];
    }

    public static function rewrite($category, $message, $params, $language)
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

        $out = $message;
        foreach ($replacements as [$s, $r]) {
            if (strpos($out, $s) !== false) {
                $out = str_replace($s, $r, $out);
            }
        }
        return [$category, $out, $params, $language];
    }
}
