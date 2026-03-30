<?php

namespace humhub\modules\reactions\widgets;

use humhub\components\behaviors\PolymorphicRelation;
use humhub\modules\reactions\assets\ReactionAsset;
use humhub\modules\reactions\models\Reaction;
use humhub\modules\reactions\Module;
use Yii;
use yii\base\Widget;
use yii\helpers\Url;

class ReactionLink extends Widget
{
    public $object;

    public function run()
    {
        ReactionAsset::register($this->getView());

        $objectModel = PolymorphicRelation::getObjectModel($this->object);
        $objectId = $this->object->id;
        $userId = Yii::$app->user->isGuest ? 0 : Yii::$app->user->id;

        $summary = Reaction::getSummary($objectModel, $objectId);
        $userReaction = null;
        if ($userId) {
            $existing = Reaction::getUserReaction($objectModel, $objectId, $userId);
            $userReaction = $existing ? $existing->reaction_type : null;
        }

        return $this->render('reactionLink', [
            'object' => $this->object,
            'objectModel' => $objectModel,
            'objectId' => $objectId,
            'summary' => $summary,
            'userReaction' => $userReaction,
            'emojis' => Module::REACTIONS,
            'toggleUrl' => Url::to(['/reactions/reaction/toggle',
                'contentModel' => $objectModel, 'contentId' => $objectId]),
            'userListUrl' => Url::to(['/reactions/reaction/user-list',
                'contentModel' => $objectModel, 'contentId' => $objectId]),
            'total' => array_sum($summary),
        ]);
    }
}
