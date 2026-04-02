<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'Following');
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><i class="fa fa-users"></i> <?= $this->title ?></strong></div>
<div class="panel-body">
<?php if (empty($items)): ?>
    <p class="text-muted text-center">You are not following any stores yet.</p>
<?php else: ?>
<div class="row cards">
<?php foreach ($items as $f): $v = $f->vendor; if (!$v) continue;
    $logoUrl = $v->logo_path ? Yii::getAlias('@web') . '/' . $v->logo_path : '';
    $coverUrl = $v->cover_path ? Yii::getAlias('@web') . '/' . $v->cover_path : '';
    $storeUrl = Url::to(['/shop/store/vendor-store', 'id' => $v->id]);
?>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12" style="margin-bottom:15px">
    <div class="panel panel-default" style="margin:0">
        <div style="height:80px;background:<?= $coverUrl ? 'url(' . Html::encode($coverUrl) . ') center/cover' : 'linear-gradient(135deg,#667eea,#764ba2)' ?>;border-radius:4px 4px 0 0"></div>
        <div class="panel-body text-center" style="margin-top:-30px">
            <a href="<?= $storeUrl ?>">
                <?php if ($logoUrl): ?>
                    <img src="<?= Html::encode($logoUrl) ?>" style="width:60px;height:60px;border-radius:3px;object-fit:cover;border:3px solid #fff" alt="">
                <?php else: ?>
                    <div style="width:60px;height:60px;border-radius:3px;background:#e8e8e8;border:3px solid #fff;display:inline-flex;align-items:center;justify-content:center"><i class="fa fa-shopping-bag" style="font-size:24px;color:#bbb"></i></div>
                <?php endif; ?>
            </a>
            <h5 style="margin:8px 0 2px"><a href="<?= $storeUrl ?>"><?= Html::encode($v->shop_name) ?></a></h5>
            <?php if ($v->tagline): ?><small class="text-muted"><?= Html::encode($v->tagline) ?></small><?php endif; ?>
            <div style="margin-top:8px">
                <a href="<?= Url::to(['/shop/store/toggle-follow', 'vendorId' => $v->id]) ?>" class="btn btn-primary btn-xs active" data-method="post"><i class="fa fa-check"></i> Following</a>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
<a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to Shop</a>
</div></div>
