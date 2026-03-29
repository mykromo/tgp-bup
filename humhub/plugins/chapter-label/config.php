<?php

use humhub\modules\chapterlabel\Events;
use humhub\modules\chapterlabel\Module;
use yii\base\Controller;

return [
    'id' => 'chapter-label',
    'class' => Module::class,
    'namespace' => 'humhub\modules\chapterlabel',
    'events' => [
        [Controller::class, Controller::EVENT_BEFORE_ACTION, [Events::class, 'onBeforeAction']],
    ],
];
