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
$uid = 'reaction-' . md5($objectModel . $objectId);
?>
<span class="reaction-container" id="<?= $uid ?>"
      data-toggle-url="<?= $toggleUrl ?>"
      data-userlist-url="<?= $userListUrl ?>"
      data-user-reaction="<?= Html::encode($userReaction) ?>">

    <?php if (!Yii::$app->user->isGuest): ?>
    <span class="reaction-trigger" style="position:relative; cursor:pointer">
        <span class="reaction-btn" data-action-click="reactions.showPicker">
            <?php if ($userReaction && isset($emojis[$userReaction])): ?>
                <?= $emojis[$userReaction] ?>
            <?php else: ?>
                👍
            <?php endif; ?>
        </span>
        <span class="reaction-picker" style="display:none; position:absolute; bottom:24px; left:0;
              background:#fff; border:1px solid #ddd; border-radius:20px; padding:4px 8px;
              box-shadow:0 2px 8px rgba(0,0,0,.15); white-space:nowrap; z-index:1000">
            <?php foreach ($emojis as $type => $emoji): ?>
                <span class="reaction-emoji" data-type="<?= $type ?>"
                      data-action-click="reactions.react"
                      title="<?= ucfirst($type) ?>"
                      style="cursor:pointer; font-size:20px; padding:2px 4px;
                             transition:transform .15s; display:inline-block"
                      onmouseover="this.style.transform='scale(1.4)'"
                      onmouseout="this.style.transform='scale(1)'"><?= $emoji ?></span>
            <?php endforeach; ?>
        </span>
    </span>
    <?php endif; ?>

    <span class="reaction-summary">
        <?php foreach ($summary as $type => $count): ?>
            <?php if ($count > 0 && isset($emojis[$type])): ?>
                <a href="<?= $userListUrl ?>&type=<?= $type ?>" data-target="#globalModal"
                   class="tt" data-toggle="tooltip" title="<?= ucfirst($type) ?>"
                   style="text-decoration:none"><?= $emojis[$type] ?><small><?= $count ?></small></a>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if ($total > 0): ?>
            <a href="<?= $userListUrl ?>" data-target="#globalModal"
               style="text-decoration:none; color:#999; font-size:11px">(<?= $total ?>)</a>
        <?php endif; ?>
    </span>
</span>
