<?php

use humhub\components\ActiveRecord;
use humhub\modules\content\widgets\WallEntryLinks;
use humhub\modules\reactions\Events;
use humhub\modules\reactions\Module;
use humhub\modules\user\models\User;

return [
    'id' => 'reactions',
    'class' => Module::class,
    'namespace' => 'humhub\modules\reactions',
    'events' => [
        [WallEntryLinks::class, WallEntryLinks::EVENT_INIT, [Events::class, 'onWallEntryLinksInit']],
        [ActiveRecord::class, ActiveRecord::EVENT_BEFORE_DELETE, [Events::class, 'onActiveRecordDelete']],
        [User::class, User::EVENT_BEFORE_DELETE, [Events::class, 'onUserDelete']],
    ],
];
