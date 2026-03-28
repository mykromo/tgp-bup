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

$this->pageTitle = Html::encode($election->title);
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <strong><?= Html::encode($election->title) ?></strong>
        <span class="label label-<?= $election->isOpen() ? 'success' : 'default' ?> pull-right" style="margin-top:2px">
            <?php if ($election->isExpired()): ?>
                <?= Yii::t('ElectionModule.base', 'Expired') ?>
            <?php elseif (!$election->isOpen()): ?>
                <?= Yii::t('ElectionModule.base', 'Closed') ?>
            <?php else: ?>
                <?= Yii::t('ElectionModule.base', 'Open') ?>
            <?php endif; ?>
        </span>
    </div>
    <div class="panel-body">
        <?php if ($election->description): ?>
            <p class="text-muted"><?= Html::encode($election->description) ?></p>
        <?php endif; ?>
        <?php if ($election->expires_at): ?>
            <p>
                <i class="fa fa-clock-o"></i>
                <?php if ($election->isExpired()): ?>
                    <span class="text-danger">
                        <?= Yii::t('ElectionModule.base', 'Expired on {date}', ['date' => Yii::$app->formatter->asDatetime($election->expires_at)]) ?>
                    </span>
                <?php else: ?>
                    <span class="text-muted">
                        <?= Yii::t('ElectionModule.base', 'Expires on {date}', ['date' => Yii::$app->formatter->asDatetime($election->expires_at)]) ?>
                    </span>
                <?php endif; ?>
            </p>
        <?php endif; ?>

        <?php if ($election->isOpen() && $isMember): ?>
            <a href="<?= $contentContainer->createUrl('/election/election/file-candidacy', ['electionId' => $election->id]) ?>"
               class="btn btn-info">
                <i class="fa fa-hand-paper-o"></i> <?= Yii::t('ElectionModule.base', 'File Candidacy') ?>
            </a>
        <?php endif; ?>

        <?php if ($election->description || $election->expires_at || ($election->isOpen() && $isMember)): ?>
            <hr>
        <?php endif; ?>

        <?php foreach ($results as $positionKey => $positionData): ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><i class="fa fa-star"></i> <?= Html::encode($positionData['label']) ?></strong>
                    <?php $hasVoted = $election->hasVoted($userId, $positionKey); ?>
                    <?php if ($hasVoted): ?>
                        <span class="label label-info pull-right">
                            <i class="fa fa-check"></i> <?= Yii::t('ElectionModule.base', 'Voted') ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="panel-body">
                    <?php if (empty($positionData['candidates'])): ?>
                        <p class="text-muted"><?= Yii::t('ElectionModule.base', 'No candidates for this position yet.') ?></p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><?= Yii::t('ElectionModule.base', 'Candidate') ?></th>
                                        <th><?= Yii::t('ElectionModule.base', 'Statement') ?></th>
                                        <th class="text-center"><?= Yii::t('ElectionModule.base', 'Votes') ?></th>
                                        <?php if ($election->isOpen() && !$hasVoted && $isMember): ?>
                                            <th class="text-center"><?= Yii::t('ElectionModule.base', 'Action') ?></th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($positionData['candidates'] as $entry): ?>
                                        <tr>
                                            <td style="white-space:nowrap">
                                                <img src="<?= $entry['user']->getProfileImage()->getUrl() ?>"
                                                     class="img-rounded" width="24" height="24"
                                                     alt="<?= Html::encode($entry['user']->displayName) ?>">
                                                <?= Html::encode($entry['user']->displayName) ?>
                                            </td>
                                            <td class="text-muted">
                                                <?= $entry['candidate']->statement ? Html::encode($entry['candidate']->statement) : '—' ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge"><?= $entry['votes'] ?></span>
                                            </td>
                                            <?php if ($election->isOpen() && !$hasVoted && $isMember): ?>
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
            <?php if ($canManage): ?>
                <?php if ($election->isOpen()): ?>
                    <a href="<?= $contentContainer->createUrl('/election/election/close', ['id' => $election->id]) ?>"
                       class="btn btn-warning pull-right"
                       data-method="post"
                       data-confirm="<?= Yii::t('ElectionModule.base', 'Are you sure you want to close this election?') ?>">
                        <i class="fa fa-lock"></i> <?= Yii::t('ElectionModule.base', 'Close Election') ?>
                    </a>
                <?php else: ?>
                    <a href="<?= $contentContainer->createUrl('/election/election/reopen', ['id' => $election->id]) ?>"
                       class="btn btn-info pull-right"
                       data-method="post">
                        <i class="fa fa-unlock"></i> <?= Yii::t('ElectionModule.base', 'Reopen Election') ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
