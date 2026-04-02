<?php
use humhub\libs\Html;
$cacheTtl = Yii::$app->getModule('shop')->settings->get('cacheTtl', 300);
?>
<div class="panel-body">
<?= Html::beginForm('', 'post') ?>
<h5><i class="fa fa-bolt"></i> Cache Configuration</h5>
<div class="form-group">
<label>Cache Duration (seconds)</label>
<input type="number" name="cacheTtl" value="<?= (int) $cacheTtl ?>" min="0" max="86400" class="form-control" style="width:200px">
<p class="help-block">0 = no caching. Default: 300 (5 min). Max: 86400 (24 hours).</p>
</div>
<hr>
<p class="text-muted"><i class="fa fa-info-circle"></i> Payment instructions and accepted methods are configured by each vendor in their store settings.</p>
<hr>
<?= Html::submitButton('<i class="fa fa-check"></i> Save Settings', ['class' => 'btn btn-primary']) ?>
<?= Html::endForm() ?>
</div>
