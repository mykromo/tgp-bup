<?php
use humhub\libs\Html;
use yii\helpers\Url;
$this->title = Yii::t('ShopModule.base', 'Edit Store Profile');
?>
<div class="panel panel-default">
<div class="panel-heading"><strong><i class="fa fa-pencil"></i> <?= $this->title ?></strong></div>
<div class="panel-body">
<?= Html::beginForm('', 'post', ['enctype' => 'multipart/form-data']) ?>

<div class="form-group">
    <label>Shop Name</label>
    <input type="text" name="shop_name" value="<?= Html::encode($vendor->shop_name) ?>" class="form-control" required maxlength="255">
</div>
<div class="form-group">
    <label>Tagline</label>
    <input type="text" name="tagline" value="<?= Html::encode($vendor->tagline) ?>" class="form-control" maxlength="255" placeholder="A short description of your store">
</div>
<div class="form-group">
    <label>Description</label>
    <textarea name="description" class="form-control" rows="4"><?= Html::encode($vendor->description) ?></textarea>
</div>
<div class="form-group">
    <label>Location</label>
    <input type="text" name="location" value="<?= Html::encode($vendor->location) ?>" class="form-control" maxlength="255">
</div>

<hr>
<h4><i class="fa fa-image"></i> Store Images</h4>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Store Logo</label>
            <?php if ($vendor->logo_path): ?>
                <div style="margin-bottom:8px"><img src="<?= Yii::getAlias('@web') . '/' . $vendor->logo_path ?>" style="max-height:80px;border-radius:50%;border:2px solid #ddd"></div>
            <?php endif; ?>
            <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp" class="form-control">
            <p class="help-block">Square image recommended. Max 2MB. Resized to 400x400.</p>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Cover Photo</label>
            <?php if ($vendor->cover_path): ?>
                <div style="margin-bottom:8px"><img src="<?= Yii::getAlias('@web') . '/' . $vendor->cover_path ?>" style="max-height:80px;max-width:100%;border-radius:4px;border:1px solid #ddd"></div>
            <?php endif; ?>
            <input type="file" name="cover" accept=".jpg,.jpeg,.png,.webp" class="form-control">
            <p class="help-block">Wide image recommended (1200x400). Max 2MB.</p>
        </div>
    </div>
</div>

<hr>
<?= Html::submitButton('<i class="fa fa-check"></i> Save', ['class' => 'btn btn-primary']) ?>
<a href="<?= Url::to(['/shop/seller/dashboard']) ?>" class="btn btn-default">Cancel</a>
<?= Html::endForm() ?>
</div></div>
