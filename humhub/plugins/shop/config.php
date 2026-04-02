<?php

use humhub\modules\admin\widgets\AdminMenu;
use humhub\modules\shop\Events;
use humhub\modules\shop\Module;
use humhub\widgets\TopMenu;

return [
    'id' => 'shop',
    'class' => Module::class,
    'namespace' => 'humhub\modules\shop',
    'urlManagerRules' => [
        'shop' => 'shop/store/index',
        'shop/admin' => 'shop/admin/index',
    ],
    'events' => [
        [TopMenu::class, TopMenu::EVENT_INIT, [Events::class, 'onTopMenuInit']],
        [AdminMenu::class, AdminMenu::EVENT_INIT, [Events::class, 'onAdminMenuInit']],
    ],
];
