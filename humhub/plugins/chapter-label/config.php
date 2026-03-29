<?php

use humhub\modules\chapterlabel\Events;
use humhub\modules\chapterlabel\Module;

return [
    'id' => 'chapter-label',
    'class' => Module::class,
    'namespace' => 'humhub\modules\chapterlabel',
    'events' => [
        ['humhub\components\Controller', 'beforeAction', [Events::class, 'onBeforeAction']],
    ],
];
