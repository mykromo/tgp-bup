<?php

use humhub\libs\Html;
use humhub\modules\election\models\Election;
use humhub\modules\content\components\ContentContainerActiveRecord;

/* @var $elections Election[] */
/* @var $canCreate bool */
/* @var $contentContainer ContentContainerActiveRecord */

$this->pageTitle = Yii::t('ElectionModule.base', 'Officer Elections');
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <strong><?= Yii::t('ElectionModule.base', 'Officer Elections') ?></strong>
        <?php if ($canCreate): ?>
            <div class="pull-right">
                <a href="<?= $contentContainer->createUrl('/election/election/create') ?>"
                   class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> <?= Yii::t('ElectionModule.base', 'Create Election') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <div class="panel-body">
        <?php if (empty($elections)): ?>
            <div class="text-center text-muted">
                <p><i class="fa fa-check-square-o" style="font-size:48px"></i></p>
                <p><?= Yii::t('ElectionModule.base', 'No elections have been created yet.') ?></p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?= Yii::t('ElectionModule.base', 'Title') ?></th>
                            <th><?= Yii::t('ElectionModule.base', 'Phase') ?></th>
                            <th><?= Yii::t('ElectionModule.base', 'Candidacy') ?></th>
                            <th><?= Yii::t('ElectionModule.base', 'Voting') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($elections as $election): ?>
                            <tr>
                                <td><?= Html::encode($election->title) ?></td>
                                <td>
                                    <span class="label <?= $election->getPhaseBadgeClass() ?>">
                                        <?= $election->getPhaseLabel() ?>
                                    </span>
                                </td>
                                <td><?= $election->candidacy_start_at ? Yii::$app->formatter->asDate($election->candidacy_start_at) . ' — ' . Yii::$app->formatter->asDate($election->candidacy_expires_at) : '—' ?></td>
                                <td><?= $election->voting_start_at ? Yii::$app->formatter->asDate($election->voting_start_at) . ' — ' . Yii::$app->formatter->asDate($election->voting_expires_at) : '—' ?></td>
                                <td>
                                    <a href="<?= $contentContainer->createUrl('/election/election/view', ['id' => $election->id]) ?>"
                                       class="btn btn-default btn-sm">
                                        <?= Yii::t('ElectionModule.base', 'View') ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <hr>
        <a href="<?= $contentContainer->createUrl('/election/officer/index') ?>" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> <?= Yii::t('ElectionModule.base', 'Back to Officers') ?>
        </a>
    </div>
</div>
