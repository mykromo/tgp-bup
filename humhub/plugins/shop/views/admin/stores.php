<?php
use humhub\libs\Html;
use humhub\modules\shop\models\Vendor;
use yii\helpers\Url;
?>
<div class="panel-body">
    <form method="get" action="<?= Url::to(['/shop/admin/stores']) ?>" class="form-inline" style="margin-bottom:15px">
        <select name="status" class="form-control input-sm" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <?php foreach (Vendor::getStatusLabels() as $k => $v): ?>
                <option value="<?= $k ?>" <?= ($selectedStatus ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
        <span class="text-muted" style="margin-left:10px"><?= count($vendors) ?> store(s)</span>
    </form>
</div>

<?php if (empty($vendors)): ?>
    <div class="panel-body text-center text-muted">No stores found.</div>
<?php else: ?>
<div class="row cards" style="padding:0 10px">
    <?php foreach ($vendors as $v):
        $logoUrl = $v->logo_path ? Yii::getAlias('@web') . '/' . $v->logo_path : '';
        $coverUrl = $v->cover_path ? Yii::getAlias('@web') . '/' . $v->cover_path : '';
        $storeUrl = Url::to(['/shop/store/vendor-store', 'id' => $v->id]);
        $productCount = $v->getActiveProductCount();
        $followerCount = $v->getFollowerCount();
    ?>
    <div class="card card-space col-lg-3 col-md-4 col-sm-6 col-xs-12">
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
                    <span class="label label-<?= Vendor::getStatusBadge($v->status) ?>"><?= Vendor::getStatusLabels()[$v->status] ?? $v->status ?></span>
                </div>
            </div>
            <div class="card-body">
                <strong class="card-title"><a href="<?= $storeUrl ?>"><?= Html::encode($v->shop_name) ?></a></strong>
                <?php if ($v->tagline): ?>
                    <div class="card-details"><?= Html::encode($v->tagline) ?></div>
                <?php elseif ($v->description): ?>
                    <div class="card-details"><?= Html::encode(mb_substr($v->description, 0, 80)) ?><?= mb_strlen($v->description) > 80 ? '...' : '' ?></div>
                <?php endif; ?>
                <div class="card-tags">
                    <span class="label label-default"><i class="fa fa-cube"></i> <?= $productCount ?> Products</span>
                    <span class="label label-default"><i class="fa fa-users"></i> <?= $followerCount ?> Followers</span>
                    <?php if ($v->location): ?>
                        <span class="label label-default"><i class="fa fa-map-marker"></i> <?= Html::encode($v->location) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= $storeUrl ?>" class="btn btn-default btn-sm"><i class="fa fa-eye"></i> View</a>
                <?php if ($v->status === Vendor::STATUS_APPROVED): ?>
                    <?= Html::beginForm(Url::to(['/shop/admin/disable-store', 'id' => $v->id]), 'post', ['style' => 'display:inline']) ?>
                        <input type="hidden" name="reason" value="">
                        <?= Html::submitButton('<i class="fa fa-ban"></i> Disable', ['class' => 'btn btn-danger btn-sm', 'data-confirm' => 'Disable this store?']) ?>
                    <?= Html::endForm() ?>
                <?php elseif ($v->status === Vendor::STATUS_SUSPENDED): ?>
                    <a href="<?= Url::to(['/shop/admin/enable-store', 'id' => $v->id]) ?>" class="btn btn-success btn-sm" data-method="post" data-confirm="Re-enable this store?"><i class="fa fa-check"></i> Enable</a>
                <?php endif; ?>
                <?php if ($v->reenable_request): ?>
                    <span class="label label-warning" title="<?= Html::encode($v->reenable_request) ?>" style="margin-left:4px"><i class="fa fa-comment"></i> Re-enable requested</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<div class="panel-body">
    <?= \yii\widgets\LinkPager::widget(['pagination' => $pagination]) ?>
</div>
<?php endif; ?>
