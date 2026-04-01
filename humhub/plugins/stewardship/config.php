<?php

use humhub\modules\space\widgets\Menu as SpaceMenu;
use humhub\modules\stewardship\Events;
use humhub\modules\stewardship\Module;

return [
    'id' => 'stewardship',
    'class' => Module::class,
    'namespace' => 'humhub\modules\stewardship',
    'events' => [
        [SpaceMenu::class, SpaceMenu::EVENT_INIT, [Events::class, 'onSpaceMenuInit']],
    ],
];
