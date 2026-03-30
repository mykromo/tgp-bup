<?php

namespace humhub\modules\reactions;

use humhub\components\behaviors\PolymorphicRelation;
use humhub\modules\like\widgets\LikeLink;
use humhub\modules\reactions\models\Reaction;
use humhub\modules\reactions\widgets\ReactionLink;
use Yii;
use yii\base\BaseObject;
use yii\base\Event;

class Events extends BaseObject
{
    public static function onWallEntryLinksInit(Event $event)
    {
        if (!Yii::$app->getModule('reactions')) {
            return;
        }
        $event->sender->removeWidget(LikeLink::class);
        $object = $event->sender->object;
        if ($object) {
            $event->sender->addWidget(ReactionLink::class, ['object' => $object], ['sortOrder' => 10]);
        }
    }

    public static function onCommentEntryLinksInit(Event $event)
    {
        if (!Yii::$app->getModule('reactions')) {
            return;
        }
        $event->sender->removeWidget(LikeLink::class);
        $object = $event->sender->object;
        if ($object) {
            $event->sender->addWidget(ReactionLink::class, ['object' => $object], ['sortOrder' => 50]);
        }
    }

    public static function onConversationEntryMenuInit(Event $event)
    {
    }

    public static function onActiveRecordDelete($event)
    {
        $record = $event->sender;
        if ($record->hasAttribute('id')) {
            Reaction::deleteAll([
                'object_id' => $record->id,
                'object_model' => PolymorphicRelation::getObjectModel($record),
            ]);
        }
    }

    public static function onUserDelete($event)
    {
        Reaction::deleteAll(['created_by' => $event->sender->id]);
    }
}
