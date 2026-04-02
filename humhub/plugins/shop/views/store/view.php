<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = Html::encode($product->name);
$images = $product->images;
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><?= $this->title ?></strong></div>
<div class="panel-body">

<?php if (!empty($images)): ?>
<div class="shop-slider" style="text-align:center;margin-bottom:20px;position:relative">
    <div class="shop-slider-main" style="overflow:hidden;max-height:400px">
        <?php foreach ($images as $i => $img): ?>
        <div class="shop-slide" style="<?= $i > 0 ? 'display:none' : '' ?>">
            <img src="<?= $img->getUrl() ?>" style="max-width:100%;max-height:400px;border-radius:4px" alt="<?= Html::encode($product->name) ?>">
        </div>
        <?php endforeach; ?>
    </div>
    <?php if (count($images) > 1): ?>
    <button class="shop-prev btn btn-default" style="position:absolute;left:5px;top:50%;transform:translateY(-50%)" onclick="shopSlide(-1)">&lsaquo;</button>
    <button class="shop-next btn btn-default" style="position:absolute;right:5px;top:50%;transform:translateY(-50%)" onclick="shopSlide(1)">&rsaquo;</button>
    <div style="margin-top:10px">
        <?php foreach ($images as $i => $img): ?>
        <img src="<?= $img->getUrl() ?>" class="shop-thumb"
             style="width:60px;height:60px;object-fit:cover;border:2px solid <?= $i === 0 ? '#337ab7' : '#ddd' ?>;border-radius:4px;cursor:pointer;margin:2px"
             onclick="shopGoTo(<?= $i ?>)">
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<script>
var shopIdx = 0;
var shopSlides = document.querySelectorAll('.shop-slide');
var shopThumbs = document.querySelectorAll('.shop-thumb');
function shopGoTo(n) {
    shopSlides[shopIdx].style.display = 'none';
    shopThumbs[shopIdx].style.borderColor = '#ddd';
    shopIdx = (n + shopSlides.length) % shopSlides.length;
    shopSlides[shopIdx].style.display = '';
    shopThumbs[shopIdx].style.borderColor = '#337ab7';
}
function shopSlide(d) { shopGoTo(shopIdx + d); }
</script>
<?php elseif ($product->image_url): ?>
<div style="text-align:center;margin-bottom:15px">
    <img src="<?= Html::encode($product->image_url) ?>" style="max-height:400px;max-width:100%;border-radius:4px">
</div>
<?php endif; ?>

<p><?= nl2br(Html::encode($product->description)) ?></p>
<h3 style="color:#337ab7"><?= $product->formatPrice() ?></h3>
<?php if ($product->stock !== null): ?><p class="text-muted"><?= $product->stock ?> in stock</p><?php endif; ?>
<?php if ($product->isInStock()): ?>
    <a href="<?= Url::to(['/shop/store/buy', 'id' => $product->id]) ?>" class="btn btn-success"><i class="fa fa-shopping-cart"></i> Buy Now</a>
<?php else: ?>
    <span class="label label-danger">Out of Stock</span>
<?php endif; ?>
<a href="<?= Url::to(['/shop/store/index']) ?>" class="btn btn-default">Back to Shop</a>
</div></div>
