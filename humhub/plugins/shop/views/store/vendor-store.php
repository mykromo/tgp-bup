<?php
use humhub\libs\Html;
use yii\helpers\Url;
humhub\modules\shop\assets\ShopAsset::register($this);
$this->title = Html::encode($vendor->shop_name);
$baseUrl = ['/shop/store/vendor-store', 'id' => $vendor->id, 'tab' => $activeTab];
?>

<?= $this->render('_store_header', ['vendor' => $vendor, 'activeTab' => $activeTab, 'isFavorited' => $isFavorited]) ?>

<?php if ($activeTab === 'about'): ?>
<div class="panel panel-default"><div class="panel-body">
    <h4>About <?= Html::encode($vendor->shop_name) ?></h4>
    <?php if ($vendor->description): ?><p><?= nl2br(Html::encode($vendor->description)) ?></p><?php endif; ?>
    <?php if ($vendor->location): ?><p><i class="fa fa-map-marker"></i> <?= Html::encode($vendor->location) ?></p><?php endif; ?>
    <?php if ($vendor->user): ?><p><i class="fa fa-user"></i> Owner: <a href="<?= $vendor->user->getUrl() ?>"><?= Html::encode($vendor->user->displayName) ?></a></p><?php endif; ?>
</div></div>

<?php elseif ($activeTab === 'categories'): ?>
<div class="panel panel-default"><div class="panel-body">
    <?php if (empty($categories)): ?>
        <p class="text-muted text-center">No categories.</p>
    <?php else: ?>
    <div class="row">
    <?php foreach ($categories as $cat):
        $count = $cat->getProducts()->where(['vendor_id' => $vendor->id, 'is_active' => 1])->count();
        if ($count == 0) continue;
    ?>
        <div class="col-sm-4 col-md-3" style="margin-bottom:12px">
            <a href="<?= Url::to(['/shop/store/vendor-store', 'id' => $vendor->id, 'category' => $cat->id]) ?>" class="btn btn-default btn-block">
                <i class="fa fa-tag"></i> <?= Html::encode($cat->name) ?> <span class="badge"><?= $count ?></span>
            </a>
        </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div></div>

<?php else: ?>

<!-- Filter & Sort Bar -->
<div class="panel panel-default"><div class="panel-body">
    <form method="get" action="<?= Url::to(['/shop/store/vendor-store']) ?>" class="form-inline" style="display:flex;flex-wrap:wrap;gap:6px;align-items:center">
        <input type="hidden" name="id" value="<?= $vendor->id ?>">
        <input type="hidden" name="tab" value="<?= Html::encode($activeTab) ?>">
        <input type="text" name="q" value="<?= Html::encode($keyword ?? '') ?>" class="form-control input-sm" placeholder="Search in store..." style="width:160px">
        <select name="category" class="form-control input-sm">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat->id ?>" <?= ($categoryFilter ?? '') == $cat->id ? 'selected' : '' ?>><?= Html::encode($cat->name) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="sort" class="form-control input-sm">
            <option value="default" <?= ($sort ?? '') === 'default' ? 'selected' : '' ?>>Default</option>
            <option value="price_asc" <?= ($sort ?? '') === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
            <option value="price_desc" <?= ($sort ?? '') === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
            <option value="newest" <?= ($sort ?? '') === 'newest' ? 'selected' : '' ?>>Newest</option>
            <option value="name" <?= ($sort ?? '') === 'name' ? 'selected' : '' ?>>Name A-Z</option>
        </select>
        <button type="submit" class="btn btn-default btn-sm"><i class="fa fa-filter"></i> Filter</button>
    </form>
</div></div>

<?php if (empty($products)): ?>
    <div class="panel panel-default"><div class="panel-body text-center text-muted" style="padding:40px">
        <i class="fa fa-cube" style="font-size:48px;color:#ddd"></i>
        <p style="margin-top:10px"><?= $activeTab === 'sale' ? 'No items on sale right now.' : 'No products found.' ?></p>
    </div></div>
<?php else: ?>
    <div class="shop-grid">
        <?php foreach ($products as $p): ?>
            <?= $this->render('_product_card', ['p' => $p]) ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php endif; ?>
