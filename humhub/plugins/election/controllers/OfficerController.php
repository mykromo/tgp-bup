<?php

namespace humhub\modules\election\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\election\models\Election;
use humhub\modules\space\models\Space;

class OfficerController extends ContentContainerController
{
    public $validContentContainerClasses = [Space::class];

    /**
     * Displays the elected officers from the most recent completed election.
     */
    public function actionIndex()
    {
        // Find the latest completed election for this space
        $election = Election::find()
            ->contentContainer($this->contentContainer)
            ->orderBy(['election.created_at' => SORT_DESC])
            ->all();

        $latestCompleted = null;
        foreach ($election as $e) {
            $e->checkAndPostResults();
            if ($latestCompleted === null && $e->isCompleted()) {
                $latestCompleted = $e;
            }
        }

        $winners = $latestCompleted ? $latestCompleted->getWinners() : [];

        return $this->render('index', [
            'election' => $latestCompleted,
            'winners' => $winners,
            'contentContainer' => $this->contentContainer,
        ]);
    }
}
