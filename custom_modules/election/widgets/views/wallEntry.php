<?php

use humhub\modules\election\models\Election;
use humhub\libs\Html;

/* @var $election Election */
?>

<div class="election-wall-entry">
    <h4>
        <i class="fa fa-check-square-o"></i>
        <?= Html::encode($election->title) ?>
    </h4>
    <?php if ($election->description): ?>
        <p class="text-muted"><?= Html::encode($election->description) ?></p>
    <?php endif; ?>
    <p>
        <span class="label <?= $election->getPhaseBadgeClass() ?>">
            <?= $election->getPhaseLabel() ?>
        </span>
    </p>
</div>
