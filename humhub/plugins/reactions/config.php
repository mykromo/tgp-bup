<?php

use humhub\components\ActiveRecord;
use humhub\modules\comment\widgets\CommentEntryLinks;
use humhub\modules\content\widgets\WallEntryLinks;
use humhub\modules\reactions\Events;
use humhub\modules\reactions\Module;
use humhub\modules\user\models\User;

return [
    'id' => 'reactions',
    'class' => Module::class,
    'namespace' => 'humhub\modules\reactions',
    'events' => [
        // Posts — replace Like with emoji reactions
        [WallEntryLinks::class, WallEntryLinks::EVENT_INIT, [Events::class, 'onWallEntryLinksInit']],
        // Comments/replies — replace Like with emoji reactions
        [CommentEntryLinks::class, CommentEntryLinks::EVENT_INIT, [Events::class, 'onCommentEntryLinksInit']],
        // Messages — add emoji reactions
        ['humhub\modules\mail\widgets\ConversationEntryMenu', 'init', [Events::class, 'onConversationEntryMenuInit']],
        // Cleanup
        [ActiveRecord::class, ActiveRecord::EVENT_BEFORE_DELETE, [Events::class, 'onActiveRecordDelete']],
        [User::class, User::EVENT_BEFORE_DELETE, [Events::class, 'onUserDelete']],
    ],
];
