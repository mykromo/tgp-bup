<?php

/**
 * Label Overwrites Configuration
 *
 * Replaces all "Space"/"Spaces" labels with "Chapter"/"Chapters"
 * across the entire HumHub system, including all modules and all languages.
 *
 * How to install:
 *   Add the following line to your protected/config/common.php, before "return $config;":
 *
 *   $config = \yii\helpers\ArrayHelper::merge(
 *       $config,
 *       require(__DIR__ . '/../humhub/config/label-overwrites.php')
 *   );
 *
 * How it works:
 *   Uses HumHub's I18N `beforeTranslateCallback` to intercept every Yii::t() call.
 *
 *   For English (source language): the callback rewrites the source message so
 *   "Space"/"Spaces" becomes "Chapter"/"Chapters" in the output.
 *
 *   For non-English languages: the original key is left intact so existing
 *   translations still resolve (e.g. 'Space' => 'Espace' in French). To also
 *   rename in other languages, place message overwrite files in
 *   protected/config/messages/{lang}/ for each module category.
 */

return [
    'components' => [
        'i18n' => [
            // Point message overwrites to files inside this directory.
            // Create files like config/messages/en-US/UserModule.profile.php
            // to override specific translation keys per module category.
            'messageOverwritePath' => '@humhub/config/messages',

            'beforeTranslateCallback' => function ($category, $message, $params, $language) {

                // Fast path: skip messages that don't contain "space" at all
                if (stripos($message, 'space') === false) {
                    return [$category, $message, $params, $language];
                }

                // Only rewrite for English (source language).
                // For other languages the original key must stay intact
                // so the translation file lookup still works.
                $isEnglish = ($language === 'en' || $language === 'en-US' || $language === 'en-GB'
                    || strpos($language, 'en') === 0);

                if (!$isEnglish) {
                    return [$category, $message, $params, $language];
                }

                // -----------------------------------------------------------
                // Exact replacements (O(1) hash lookup for common labels)
                // -----------------------------------------------------------
                static $exact = [
                    'Space' => 'Chapter',
                    'Spaces' => 'Chapters',
                    '<strong>Spaces</strong>' => '<strong>Chapters</strong>',
                    '<strong>Space</strong> followers' => '<strong>Chapter</strong> followers',
                    '<strong>Space</strong> members' => '<strong>Chapter</strong> members',
                    '<strong>Space</strong> menu' => '<strong>Chapter</strong> menu',
                    '<strong>Space</strong> Menu' => '<strong>Chapter</strong> Menu',
                    '<strong>Space</strong> tags' => '<strong>Chapter</strong> tags',
                    '<strong>New</strong> spaces' => '<strong>New</strong> chapters',
                    '<strong>Leave</strong> Space' => '<strong>Leave</strong> Chapter',
                    '<strong>About</strong> the Space' => '<strong>About</strong> the Chapter',
                    'Space Name' => 'Chapter Name',
                    'Space Visibility' => 'Chapter Visibility',
                    'Space directory' => 'Chapter directory',
                    'Space is invisible!' => 'Chapter is invisible!',
                    'Space Settings' => 'Chapter Settings',
                    'Advanced Spaces Search' => 'Advanced Chapters Search',
                    'Space default state' => 'Chapter default state',
                    'Default Space' => 'Default Chapter',
                    'Default space' => 'Default chapter',
                    'Default Space(s)' => 'Default Chapter(s)',
                    'Add Space' => 'Add Chapter',
                    'Add new space' => 'Add new chapter',
                    'My spaces' => 'My chapters',
                    'Manage Spaces' => 'Manage Chapters',
                    '<strong>Manage</strong> Spaces' => '<strong>Manage</strong> Chapters',
                    'Create Private Spaces' => 'Create Private Chapters',
                    'Create Public Spaces' => 'Create Public Chapters',
                    'Default Space Permissions' => 'Default Chapter Permissions',
                    'No spaces found.' => 'No chapters found.',
                    '<strong>Guide:</strong> Spaces' => '<strong>Guide:</strong> Chapters',
                    '<strong>Start</strong> space guide' => '<strong>Start</strong> chapter guide',
                    'Find Spaces by their description or by their tags' => 'Find Chapters by their description or by their tags',
                ];

                if (isset($exact[$message])) {
                    return [$category, $exact[$message], $params, $language];
                }

                // -----------------------------------------------------------
                // Generic str_replace fallback for all other messages.
                // Replaces "Spaces" before "Space" to avoid double-replacement.
                // -----------------------------------------------------------
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
            },
        ],
    ],
];
