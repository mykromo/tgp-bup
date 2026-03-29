<?php

use humhub\libs\Html;
use humhub\modules\election\models\Election;
use humhub\modules\content\components\ContentContainerActiveRecord;

/* @var $election Election */
/* @var $results array */
/* @var $userId int */
/* @var $isMember bool */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $canManage bool */

$phase = $election->getPhase();
$this->pageTitle = Html::encode($election->title);
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <strong><?= Html::encode($election->title) ?></strong>
        <span class="label <?= $election->getPhaseBadgeClass() ?> pull-right" style="margin-top:2px">
            <?= $election->getPhaseLabel() ?>
        </span>
    </div>
    <div class="panel-body">
        <?php if ($election->description): ?>
            <p class="text-muted"><?= Html::encode($election->description) ?></p>
        <?php endif; ?>

        <!-- Timeline -->
        <div class="well well-sm">
            <div class="row">
                <div class="col-sm-6">
                    <i class="fa fa-pencil-square-o"></i>
                    <strong><?= Yii::t('ElectionModule.base', 'Filing of Candidacy:') ?></strong><br>
                    <?= Yii::$app->formatter->asDatetime($election->candidacy_start_at) ?>
                    &mdash;
                    <?= Yii::$app->formatter->asDatetime($election->candidacy_expires_at) ?>
                    <?php if ($election->isCandidacyOpen()): ?>
                        <span class="label label-info"><?= Yii::t('ElectionModule.base', 'Open Now') ?></span>
                    <?php endif; ?>
                </div>
                <div class="col-sm-6">
                    <i class="fa fa-check-square-o"></i>
                    <strong><?= Yii::t('ElectionModule.base', 'Voting:') ?></strong><br>
                    <?= Yii::$app->formatter->asDatetime($election->voting_start_at) ?>
                    &mdash;
                    <?= Yii::$app->formatter->asDatetime($election->voting_expires_at) ?>
                    <?php if ($election->isVotingOpen()): ?>
                        <span class="label label-success"><?= Yii::t('ElectionModule.base', 'Open Now') ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- File Candidacy button (only during candidacy phase) -->
        <?php if ($election->isCandidacyOpen() && $isMember): ?>
            <a href="<?= $contentContainer->createUrl('/election/election/file-candidacy', ['electionId' => $election->id]) ?>"
               class="btn btn-info btn-lg btn-block" style="margin-bottom:15px">
                <i class="fa fa-hand-paper-o"></i> <?= Yii::t('ElectionModule.base', 'File Your Candidacy') ?>
            </a>
        <?php endif; ?>

        <hr>

        <!-- Positions & Candidates -->
        <?php foreach ($results as $positionKey => $positionData): ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><?= Html::encode($positionData['label']) ?></strong>
                    <?php $hasVoted = $election->hasVoted($userId, $positionKey); ?>
                    <?php if ($hasVoted): ?>
                        <span class="label label-info pull-right">
                            <i class="fa fa-check"></i> <?= Yii::t('ElectionModule.base', 'Voted') ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="panel-body">
                    <?php if (empty($positionData['candidates'])): ?>
                        <p class="text-muted">
                            <?= $election->isCandidacyOpen()
                                ? Yii::t('ElectionModule.base', 'No candidates yet. Be the first to file!')
                                : Yii::t('ElectionModule.base', 'No candidates for this position.') ?>
                        </p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><?= Yii::t('ElectionModule.base', 'Candidate') ?></th>
                                        <th><?= Yii::t('ElectionModule.base', 'Statement') ?></th>
                                        <?php if ($election->isVotingOpen() || $election->isCompleted()): ?>
                                            <th class="text-center"><?= Yii::t('ElectionModule.base', 'Votes') ?></th>
                                        <?php endif; ?>
                                        <?php if ($election->isVotingOpen() && !$hasVoted && $isMember): ?>
                                            <th class="text-center"><?= Yii::t('ElectionModule.base', 'Action') ?></th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($positionData['candidates'] as $i => $entry): ?>
                                        <tr<?= ($election->isCompleted() && $i === 0 && $entry['votes'] > 0) ? ' class="success"' : '' ?>>
                                            <td style="white-space:nowrap">
                                                <img src="<?= $entry['user']->getProfileImage()->getUrl() ?>"
                                                     class="img-rounded" width="24" height="24"
                                                     alt="<?= Html::encode($entry['user']->displayName) ?>">
                                                <?= Html::encode($entry['user']->displayName) ?>
                                                <?php if ($election->isCompleted() && $i === 0 && $entry['votes'] > 0): ?>
                                                    <i class="fa fa-trophy text-warning"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-muted">
                                                <?= $entry['candidate']->statement ? Html::encode($entry['candidate']->statement) : '—' ?>
                                            </td>
                                            <?php if ($election->isVotingOpen() || $election->isCompleted()): ?>
                                                <td class="text-center">
                                                    <span class="badge"><?= $entry['votes'] ?></span>
                                                </td>
                                            <?php endif; ?>
                                            <?php if ($election->isVotingOpen() && !$hasVoted && $isMember): ?>
                                                <td class="text-center">
                                                    <?= Html::beginForm($contentContainer->createUrl('/election/election/vote'), 'post') ?>
                                                        <?= Html::hiddenInput('electionId', $election->id) ?>
                                                        <?= Html::hiddenInput('candidateId', $entry['candidate']->id) ?>
                                                        <?= Html::hiddenInput('position', $positionKey) ?>
                                                        <?= Html::submitButton(
                                                            '<i class="fa fa-thumbs-up"></i> ' . Yii::t('ElectionModule.base', 'Vote'),
                                                            ['class' => 'btn btn-success btn-xs']
                                                        ) ?>
                                                    <?= Html::endForm() ?>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <hr>
        <div>
            <a href="<?= $contentContainer->createUrl('/election/election/index') ?>" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> <?= Yii::t('ElectionModule.base', 'Back to Elections') ?>
            </a>
            <?php if ($canManage && $election->isOpen()): ?>
                <a href="<?= $contentContainer->createUrl('/election/election/cancel', ['id' => $election->id]) ?>"
                   class="btn btn-danger pull-right" style="margin-left:5px"
                   data-method="post"
                   data-confirm="<?= Yii::t('ElectionModule.base', 'Are you sure you want to cancel this election? This cannot be undone and will not affect current officers.') ?>">
                    <i class="fa fa-ban"></i> <?= Yii::t('ElectionModule.base', 'Cancel Election') ?>
                </a>
                <a href="<?= $contentContainer->createUrl('/election/election/close', ['id' => $election->id]) ?>"
                   class="btn btn-warning pull-right"
                   data-method="post"
                   data-confirm="<?= Yii::t('ElectionModule.base', 'Are you sure you want to close this election?') ?>">
                    <i class="fa fa-lock"></i> <?= Yii::t('ElectionModule.base', 'Close Election') ?>
                </a>
            <?php elseif ($canManage && $phase === 'closed'): ?>
                <a href="<?= $contentContainer->createUrl('/election/election/reopen', ['id' => $election->id]) ?>"
                   class="btn btn-info pull-right" data-method="post">
                    <i class="fa fa-unlock"></i> <?= Yii::t('ElectionModule.base', 'Reopen Election') ?>
                </a>
            <?php elseif ($canManage && $phase === 'cancelled'): ?>
                <span class="label label-warning pull-right" style="margin-top:5px; font-size:13px">
                    <i class="fa fa-ban"></i> <?= Yii::t('ElectionModule.base', 'This election has been cancelled') ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
</div>
