<?php

use humhub\libs\Html;
use humhub\libs\Helpers;
use humhub\modules\election\models\Election;

/* @var $originator \humhub\modules\user\models\User */
/* @var $source Election */

$winners = $source->getWinners();
?>

<?= Yii::t('ElectionModule.base', 'The election <strong>{title}</strong> has been completed.', [
    'title' => Html::encode(Helpers::truncateText($source->title, 40)),
]) ?>

<?php if (!empty($winners)): ?>
    <div style="margin-top:8px">
        <?php foreach ($winners as $w): ?>
            <div style="margin:4px 0">
                <strong><?= Html::encode($w['position']) ?></strong>:
                <?= Html::encode($w['user']->displayName) ?>
                <span class="text-muted">(<?= $w['votes'] ?> <?= Yii::t('ElectionModule.base', 'votes') ?>)</span>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
