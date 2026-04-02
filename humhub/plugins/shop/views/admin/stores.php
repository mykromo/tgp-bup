<?php
use humhub\libs\Html;
use humhub\modules\shop\models\Vendor;
use yii\helpers\Url;
humhub\assets\CardsAsset::register($this);
?>
<div class="panel-body">
    <form method="get" action="<?= Url::to(['/shop/admin/stores']) ?>" class="form-inline" style="margin-bottom:15px">
        <a href="<?= Url::to(['/shop/admin/index']) ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
        <select name="status" class="form-control input-sm" style="margin-left:10px" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <?php foreach (Vendor::getStatusLabels() as $k => $v): ?>
                <option value="<?= $k ?>" <?= ($selectedStatus ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
        <span class="text-muted" style="margin-left:10px"><?= count($vendors) ?> store(s)</span>
    </form>

    <?php if (empty($vendors)): ?>
        <p class="text-muted text-center">No stores found.</p>
    <?php else: ?>
    <div class="row">
        <?php foreach ($vendors as $v):
            $logoUrl = $v->logo_path ? Yii::getAlias('@web') . '/' . $v->logo_path : '';
            $coverUrl = $v->cover_path ? Yii::getAlias('@web') . '/' . $v->cover_path : '';
            $storeUrl = Url::to(['/shop/store/vendor-store', 'id' => $v->id]);
            $followerCount = $v->getFollowerCount();
            $coverBg = $coverUrl ? 'url(' . Html::encode($coverUrl) . ') center/cover no-repeat' : '#d5d5d5';
        ?>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12" style="margin-bottom:20px">
            <div style="background:#fff;border:1px solid #ddd;border-radius:4px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.1);transition:box-shadow .2s">
                <!-- Cover -->
                <div style="height:100px;background:<?= $coverBg ?>;position:relative">
                    <div style="position:absolute;top:8px;right:10px">
                        <i class="fa fa-users" style="color:#59d6e4"></i>
                        <span style="color:#59d6e4;font-weight:600"><?= $followerCount ?></span>
                    </div>
                </div>
                <!-- Logo -->
                <div style="padding:0 15px;margin-top:-40px;position:relative;z-index:1">
                    <a href="<?= $storeUrl ?>">
                        <?php if ($logoUrl): ?>
                            <img src="<?= Html::encode($logoUrl) ?>" style="width:80px;height:80px;border-radius:4px;object-fit:cover;border:3px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,.15)" alt="">
                        <?php else: ?>
                            <div style="width:80px;height:80px;border-radius:4px;background:<?= $v->getPlaceholderColor() ?>;border:3px solid #fff;display:flex;align-items:center;justify-content:center;box-shadow:0 1px 4px rgba(0,0,0,.15)">
                                <span style="font-size:26px;font-weight:700;color:#fff"><?= Html::encode($v->getInitials()) ?></span>
                            </div>
                        <?php endif; ?>
                    </a>
                </div>
                <!-- Body -->
                <div style="padding:10px 15px 12px">
                    <strong style="font-size:14px"><a href="<?= $storeUrl ?>" style="color:#333;text-decoration:none"><?= Html::encode($v->shop_name) ?></a></strong>
                    <?php if ($v->tagline): ?>
                        <div style="color:#888;font-size:13px;margin-top:2px"><?= Html::encode($v->tagline) ?></div>
                    <?php endif; ?>
                    <?php if ($v->user): ?>
                        <div style="color:#aaa;font-size:12px;margin-top:4px"><i class="fa fa-user"></i> <?= Html::encode($v->user->displayName) ?></div>
                    <?php endif; ?>
                </div>
                <!-- Footer -->
                <div style="padding:8px 15px;border-top:1px solid #eee;display:flex;align-items:center;justify-content:space-between">
                    <span class="label label-<?= Vendor::getStatusBadge($v->status) ?>"><i class="fa fa-user"></i> <?= Vendor::getStatusLabels()[$v->status] ?? $v->status ?></span>
                    <div>
                        <?php if ($v->status === Vendor::STATUS_APPROVED): ?>
                            <?= Html::beginForm(Url::to(['/shop/admin/disable-store', 'id' => $v->id]), 'post', ['style' => 'display:inline']) ?>
                                <input type="hidden" name="reason" value="">
                                <?= Html::submitButton('<i class="fa fa-ban"></i>', ['class' => 'btn btn-danger btn-xs', 'data-confirm' => 'Disable this store?', 'title' => 'Disable']) ?>
                            <?= Html::endForm() ?>
                        <?php elseif ($v->status === Vendor::STATUS_SUSPENDED): ?>
                            <a href="<?= Url::to(['/shop/admin/enable-store', 'id' => $v->id]) ?>" class="btn btn-success btn-xs" data-method="post" data-confirm="Re-enable?" title="Enable"><i class="fa fa-check"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if ($pagination->pageCount > 1): ?>
        <?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
    <?php endif; ?>
    <?php endif; ?>
</div>
