<?php
use humhub\libs\Html;
use yii\helpers\Url;
use humhub\modules\shop\models\Wishlist;
humhub\modules\shop\assets\ShopAsset::register($this);
$this->title = Html::encode($product->name);
$images = $product->images;
$uid = Yii::$app->user->isGuest ? 0 : Yii::$app->user->id;
$wishlisted = $uid ? Wishlist::isWishlisted($uid, $product->id) : false;
?>
<div class="panel panel-default">
<div class="panel-heading">
    <strong><?= $this->title ?></strong>
    <div class="pull-right">
        <?php if ($uid): ?>
        <a href="#" class="shop-wishlist-btn <?= $wishlisted ? 'active' : '' ?>" data-url="<?= Url::to(['/shop/store/toggle-wishlist', 'productId' => $product->id]) ?>" style="font-size:20px">
            <i class="fa fa-heart<?= $wishlisted ? '' : '-o' ?>"></i>
        </a>
        <?php endif; ?>
    </div>
</div>
<div class="panel-body">
<div class="row">
<div class="col-md-6">
<?php if (!empty($images)): ?>
<div class="shop-slider">
    <div class="shop-slider-main" style="text-align:center;min-height:300px;display:flex;align-items:center;justify-content:center">
        <?php foreach ($images as $i => $img): ?>
        <div class="shop-slide" style="<?= $i > 0 ? 'display:none' : '' ?>">
            <img src="<?= $img->getUrl() ?>" style="max-width:100%;max-height:380px;border-radius:6px" alt="<?= Html::encode($product->name) ?>">
        </div>
        <?php endforeach; ?>
    </div>
    <?php if ($product->isOnSale()): ?><span class="shop-sale-badge" style="position:absolute;top:18px;right:18px">SALE</span><?php endif; ?>
    <?php if (count($images) > 1): ?>
    <button class="btn btn-default shop-slider-arrows left" onclick="shopSlide(-1)">&lsaquo;</button>
    <button class="btn btn-default shop-slider-arrows right" onclick="shopSlide(1)">&rsaquo;</button>
    <div class="shop-slider-nav">
        <?php foreach ($images as $i => $img): ?>
        <img src="<?= $img->getUrl() ?>" class="<?= $i === 0 ? 'active' : '' ?>" onclick="shopGoTo(<?= $i ?>)">
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<script>
var shopIdx=0,shopSlides=document.querySelectorAll('.shop-slide'),shopThumbs=document.querySelectorAll('.shop-slider-nav img');
function shopGoTo(n){shopSlides[shopIdx].style.display='none';if(shopThumbs[shopIdx])shopThumbs[shopIdx].classList.remove('active');shopIdx=(n+shopSlides.length)%shopSlides.length;shopSlides[shopIdx].style.display='';if(shopThumbs[shopIdx])shopThumbs[shopIdx].classList.add('active');}
function shopSlide(d){shopGoTo(shopIdx+d);}
</script>
<?php elseif ($product->image_url): ?>
<div style="text-align:center"><img src="<?= Html::encode($product->image_url) ?>" style="max-height:380px;max-width:100%;border-radius:6px"></div>
<?php endif; ?>
</div>
<div class="col-md-6">
    <h3 style="margin-top:0"><?= Html::encode($product->name) ?></h3>
    <?php if ($product->category): ?><span class="label label-default"><i class="fa fa-tag"></i> <?= Html::encode($product->category->name) ?></span><?php endif; ?>
    <?php if ($product->vendor): ?><span class="label label-info"><i class="fa fa-store"></i> <a href="<?= Url::to(['/shop/store/vendor-store', 'id' => $product->vendor_id]) ?>" style="color:#fff"><?= Html::encode($product->vendor->shop_name) ?></a></span><?php endif; ?>
    <?php if ($product->location): ?><span class="label label-default"><i class="fa fa-map-marker"></i> <?= Html::encode($product->location) ?></span><?php endif; ?>

    <div class="shop-price" style="font-size:24px;margin:15px 0"><?= $product->formatPrice() ?></div>

    <?php if ($product->stock !== null): ?><p class="text-muted"><?= $product->stock ?> in stock</p><?php endif; ?>

    <?php if (!empty($product->variants)): ?>
    <h5>Options</h5>
    <div style="margin-bottom:10px">
        <?php foreach ($product->variants as $v): ?>
        <span class="label label-default" style="font-size:12px;margin:2px"><?= Html::encode($v->name) ?> (<?= $v->formatPrice() ?>)<?= $v->stock !== null ? ' · ' . $v->stock . ' left' : '' ?></span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <p style="margin-top:15px"><?= nl2br(Html::encode($product->description)) ?></p>

    <?php if ($product->isInStock()): ?>
        <a href="<?= Url::to(['/shop/store/buy', 'id' => $product->id]) ?>" class="btn btn-success btn-lg"><i class="fa fa-shopping-cart"></i> Buy Now</a>
    <?php else: ?>
        <span class="label label-danger" style="font-size:14px">Out of Stock</span>
    <?php endif; ?>
</div>
</div>
<hr>
<a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to Shop</a>
</div></div>
