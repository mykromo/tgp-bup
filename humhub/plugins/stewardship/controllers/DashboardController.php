<?php

namespace humhub\modules\stewardship\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\stewardship\models\Fund;
use humhub\modules\stewardship\models\Grant;
use humhub\modules\stewardship\models\Transaction;
use humhub\modules\stewardship\permissions\ManageFinances;
use humhub\modules\space\models\Space;

class DashboardController extends ContentContainerController
{
    public $validContentContainerClasses = [Space::class];

    /**
     * Financial dashboard — visible to all chapter members.
     * Management actions only shown to admins.
     */
    public function actionIndex()
    {
        $spaceId = $this->contentContainer->id;
        $funds = Fund::find()->where(['space_id' => $spaceId, 'is_active' => 1])->all();
        $grants = Grant::find()->where(['space_id' => $spaceId, 'status' => 'active'])->all();
        $recentTxns = Transaction::find()
            ->where(['space_id' => $spaceId, 'is_voided' => 0])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(10)->all();

        $totalByType = [];
        foreach (Fund::getTypeLabels() as $type => $label) {
            $totalByType[$type] = (float) Fund::find()
                ->where(['space_id' => $spaceId, 'fund_type' => $type, 'is_active' => 1])
                ->sum('balance') ?: 0;
        }

        return $this->render('index', [
            'funds' => $funds,
            'grants' => $grants,
            'recentTxns' => $recentTxns,
            'totalByType' => $totalByType,
            'contentContainer' => $this->contentContainer,
            'canManage' => $this->contentContainer->permissionManager->can(ManageFinances::class),
        ]);
    }
}
