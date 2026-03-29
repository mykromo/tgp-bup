<?php

use humhub\libs\Html;

/* @var $originator \humhub\modules\user\models\User|null */
/* @var $source \humhub\modules\election\models\Election|null */

if (!$source) {
    return;
}
?>

<?= Yii::t('ElectionModule.base', 'The election <strong>{title}</strong> has been completed.', [
    'title' => Html::encode($source->title),
]) ?>

<?php
$winners = $source->getWinners();
if (!empty($winners)): ?>
<div style="margin-top:5px">
    <?php foreach ($winners as $w): ?>
    <div>
        <strong><?= Html::encode($w['position']) ?></strong> —
        <?= Html::encode($w['user']->displayName) ?>
        <small class="text-muted">(<?= $w['votes'] ?> <?= Yii::t('ElectionModule.base', 'votes') ?>)</small>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
