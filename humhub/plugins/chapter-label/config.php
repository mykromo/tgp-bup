<?php

use humhub\components\Application;
use humhub\modules\chapterlabel\Events;
use humhub\modules\chapterlabel\Module;

return [
    'id' => 'chapter-label',
    'class' => Module::class,
    'namespace' => 'humhub\modules\chapterlabel',
    'events' => [
        [Application::class, 'onInit', [Events::class, 'onInit']],
    ],
];
