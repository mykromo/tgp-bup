<?php

use humhub\modules\space\widgets\Menu as SpaceMenu;
use humhub\modules\election\Events;
use humhub\modules\election\Module;

return [
    'id' => 'election',
    'class' => Module::class,
    'namespace' => 'humhub\modules\election',
    'events' => [
        [SpaceMenu::class, SpaceMenu::EVENT_INIT, [Events::class, 'onSpaceMenuInit']],
    ],
];
