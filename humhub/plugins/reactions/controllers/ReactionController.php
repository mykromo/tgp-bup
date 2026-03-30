<?php

namespace humhub\modules\reactions\controllers;

use humhub\components\behaviors\AccessControl;
use humhub\components\behaviors\PolymorphicRelation;
use humhub\modules\content\components\ContentAddonController;
use humhub\modules\reactions\models\Reaction;
use humhub\modules\reactions\Module;
use humhub\modules\user\widgets\UserListBox;
use humhub\modules\user\models\User;
use Yii;
use yii\web\HttpException;

class ReactionController extends ContentAddonController
{
    public function behaviors()
    {
        return [
            'acl' => [
                'class' => AccessControl::class,
                'guestAllowedActions' => ['summary'],
            ],
        ];
    }

    /**
     * Toggle a reaction. If user already reacted with same type, remove it.
     * If different type, switch it. If no reaction, create it.
     */
    public function actionToggle()
    {
        $this->forcePostRequest();
        Yii::$app->response->format = 'json';

        $type = Yii::$app->request->post('type', Yii::$app->request->get('type'));
        if (!$type || !isset(Module::REACTIONS[$type])) {
            throw new HttpException(400, 'Invalid reaction type');
        }

        $userId = Yii::$app->user->id;
        $existing = Reaction::getUserReaction($this->contentModel, $this->contentId, $userId);

        if ($existing) {
            if ($existing->reaction_type === $type) {
                // Same type — remove reaction
                $existing->delete();
            } else {
                // Different type — switch
                $existing->reaction_type = $type;
                $existing->save(false);
            }
        } else {
            // New reaction
            $reaction = new Reaction();
            $reaction->object_model = $this->contentModel;
            $reaction->object_id = $this->contentId;
            $reaction->reaction_type = $type;
            $reaction->save();
        }

        return $this->actionSummary();
    }

    /**
     * Returns JSON summary of reactions for the object.
     */
    public function actionSummary()
    {
        Yii::$app->response->format = 'json';

        $summary = Reaction::getSummary($this->contentModel, $this->contentId);
        $userReaction = null;

        if (!Yii::$app->user->isGuest) {
            $existing = Reaction::getUserReaction($this->contentModel, $this->contentId, Yii::$app->user->id);
            $userReaction = $existing ? $existing->reaction_type : null;
        }

        $total = array_sum($summary);

        return [
            'summary' => $summary,
            'userReaction' => $userReaction,
            'total' => $total,
            'emojis' => Module::REACTIONS,
        ];
    }

    /**
     * Shows users who reacted with a specific type.
     */
    public function actionUserList()
    {
        $type = Yii::$app->request->get('type');

        $query = User::find()
            ->innerJoin('reaction', 'reaction.created_by = user.id')
            ->where([
                'reaction.object_model' => $this->contentModel,
                'reaction.object_id' => $this->contentId,
            ]);

        if ($type && isset(Module::REACTIONS[$type])) {
            $query->andWhere(['reaction.reaction_type' => $type]);
        }

        $query->orderBy('reaction.created_at DESC');

        $emoji = ($type && isset(Module::REACTIONS[$type])) ? Module::REACTIONS[$type] . ' ' : '';
        $title = $emoji . Yii::t('ReactionsModule.base', 'Reactions');

        return $this->renderAjaxContent(UserListBox::widget(['query' => $query, 'title' => $title]));
    }
}
