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
    <div class="row cards">
        <?php foreach ($vendors as $v):
            $logoUrl = $v->logo_path ? Yii::getAlias('@web') . '/' . $v->logo_path : '';
            $coverUrl = $v->cover_path ? Yii::getAlias('@web') . '/' . $v->cover_path : '';
            $storeUrl = Url::to(['/shop/store/vendor-store', 'id' => $v->id]);
            $followerCount = $v->getFollowerCount();
        ?>
        <div class="card card-space col-lg-4 col-md-6 col-sm-6 col-xs-12">
            <div class="card-panel<?= $v->status === Vendor::STATUS_SUSPENDED ? ' card-archived' : '' ?>">
                <div class="card-bg-image" <?= $coverUrl ? 'style="background-image:url(\'' . Html::encode($coverUrl) . '\')"' : '' ?>></div>
                <div class="card-header">
                    <a href="<?= $storeUrl ?>" class="card-image-link">
                        <?php if ($logoUrl): ?>
                            <img src="<?= Html::encode($logoUrl) ?>" class="space-profile-image" style="width:94px;height:94px;border-radius:3px;object-fit:cover;border:3px solid #fff" alt="">
                        <?php else: ?>
                            <div style="width:94px;height:94px;border-radius:3px;background:#e8e8e8;border:3px solid #fff;display:flex;align-items:center;justify-content:center">
                                <i class="fa fa-shopping-bag" style="font-size:36px;color:#bbb"></i>
                            </div>
                        <?php endif; ?>
                    </a>
                    <div class="card-icons">
                        <i class="fa fa-users" style="color:#59d6e4"></i> <span style="color:#59d6e4;font-weight:600"><?= $followerCount ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <strong class="card-title"><a href="<?= $storeUrl ?>"><?= Html::encode($v->shop_name) ?></a></strong>
                    <?php if ($v->tagline): ?>
                        <div class="card-details"><?= Html::encode($v->tagline) ?></div>
                    <?php elseif ($v->description): ?>
                        <div class="card-details"><?= Html::encode(mb_substr($v->description, 0, 100)) ?><?= mb_strlen($v->description) > 100 ? '...' : '' ?></div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <span class="label label-<?= Vendor::getStatusBadge($v->status) ?>"><i class="fa fa-user"></i> <?= Vendor::getStatusLabels()[$v->status] ?? $v->status ?></span>
                    <?php if ($v->status === Vendor::STATUS_APPROVED): ?>
                        <?= Html::beginForm(Url::to(['/shop/admin/disable-store', 'id' => $v->id]), 'post', ['style' => 'display:inline;float:right']) ?>
                            <input type="hidden" name="reason" value="">
                            <?= Html::submitButton('<i class="fa fa-ban"></i>', ['class' => 'btn btn-danger btn-xs', 'data-confirm' => 'Disable this store?', 'title' => 'Disable']) ?>
                        <?= Html::endForm() ?>
                    <?php elseif ($v->status === Vendor::STATUS_SUSPENDED): ?>
                        <a href="<?= Url::to(['/shop/admin/enable-store', 'id' => $v->id]) ?>" class="btn btn-success btn-xs pull-right" data-method="post" data-confirm="Re-enable?" title="Enable"><i class="fa fa-check"></i></a>
                    <?php endif; ?>
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
