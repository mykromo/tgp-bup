<?php

namespace humhub\modules\chapterlabel;

use Yii;
use yii\base\BaseObject;

class Events extends BaseObject
{
    private static $applied = false;

    public static function onBeforeAction($event)
    {
        if (self::$applied) {
            return;
        }
        self::$applied = true;

        try {
            $module = Yii::$app->getModule('chapter-label');
            if ($module === null) {
                return;
            }

            Yii::$app->i18n->messageOverwritePath = $module->getBasePath() . '/messages';
            Yii::$app->i18n->beforeTranslateCallback = [static::class, 'rewrite'];
        } catch (\Throwable $e) {
            // Fail silently
        }
    }

    public static function rewrite($category, $message, $params, $language)
    {
        if (stripos($message, 'space') === false) {
            return [$category, $message, $params, $language];
        }
        if (strpos($language, 'en') !== 0) {
            return [$category, $message, $params, $language];
        }

        $out = str_replace(
            ['Spaces', 'spaces', 'SPACES', 'Space', 'space', 'SPACE'],
            ['Chapters', 'chapters', 'CHAPTERS', 'Chapter', 'chapter', 'CHAPTER'],
            $message
        );

        return [$category, $out, $params, $language];
    }
}
