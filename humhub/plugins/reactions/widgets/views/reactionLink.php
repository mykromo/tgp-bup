<?php
use yii\helpers\Html;
/* @var $objectModel string */
/* @var $objectId int */
/* @var $summary array */
/* @var $userReaction string|null */
/* @var $emojis array */
/* @var $toggleUrl string */
/* @var $userListUrl string */
/* @var $total int */
$uid = 'rc-' . md5($objectModel . $objectId);
$reacted = $userReaction && isset($emojis[$userReaction]);
?>
<span class="reaction-container" id="<?= $uid ?>"
      data-toggle-url="<?= Html::encode($toggleUrl) ?>"
      data-userlist-url="<?= Html::encode($userListUrl) ?>"
      data-user-reaction="<?= Html::encode($userReaction) ?>">

    <?php if (!Yii::$app->user->isGuest): ?>
    <span class="reaction-trigger">
        <a href="javascript:;" class="reaction-btn-toggle">
            <span class="reaction-btn"><?= $reacted ? $emojis[$userReaction] : '👍' ?></span>
            <span class="reaction-label"><?= $reacted
                ? Yii::t('ReactionsModule.base', ucfirst($userReaction))
                : Yii::t('ReactionsModule.base', 'Like') ?></span>
        </a>
        <span class="reaction-picker">
            <?php foreach ($emojis as $type => $emoji): ?>
                <a href="javascript:;" class="reaction-emoji-btn"
                   data-type="<?= $type ?>"
                   title="<?= ucfirst($type) ?>"><?= $emoji ?></a>
            <?php endforeach; ?>
        </span>
    </span>
    <?php endif; ?>

    <span class="reaction-summary">
        <?php foreach ($summary as $type => $count): ?>
            <?php if ($count > 0 && isset($emojis[$type])): ?>
                <a href="<?= Html::encode($userListUrl) ?>&type=<?= $type ?>"
                   data-target="#globalModal"
                   title="<?= ucfirst($type) ?>"><?= $emojis[$type] ?><small><?= $count ?></small></a>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if ($total > 0): ?>
            <a href="<?= Html::encode($userListUrl) ?>" data-target="#globalModal"
               class="reaction-total">(<?= $total ?>)</a>
        <?php endif; ?>
    </span>
</span>
