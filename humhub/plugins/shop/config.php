<?php

use humhub\modules\shop\Events;
use humhub\modules\shop\Module;
use humhub\widgets\TopMenu;

return [
    'id' => 'shop',
    'class' => Module::class,
    'namespace' => 'humhub\modules\shop',
    'urlManagerRules' => [
        'shop' => 'shop/store/index',
        'shop/admin' => 'shop/admin/products',
    ],
    'events' => [
        [TopMenu::class, TopMenu::EVENT_INIT, [Events::class, 'onTopMenuInit']],
    ],
];
