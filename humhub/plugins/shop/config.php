<?php

use humhub\modules\space\widgets\Menu as SpaceMenu;
use humhub\modules\shop\Events;
use humhub\modules\shop\Module;

return [
    'id' => 'shop',
    'class' => Module::class,
    'namespace' => 'humhub\modules\shop',
    'events' => [
        [SpaceMenu::class, SpaceMenu::EVENT_INIT, [Events::class, 'onSpaceMenuInit']],
    ],
];
