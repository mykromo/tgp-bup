<?php
/* @var $url string base export URL */
$sep = (strpos($url, '?') !== false) ? '&' : '?';
$uid = 'exp-' . md5($url);
?>
<form class="form-inline" style="display:inline" action="" method="get" id="<?= $uid ?>">
    <select class="form-control input-sm" id="<?= $uid ?>-fmt" style="width:auto">
        <option value="csv">CSV</option>
        <option value="xlsx">XLSX</option>
        <option value="html">HTML</option>
    </select>
    <a href="#" class="btn btn-default btn-sm" onclick="window.location='<?= $url . $sep ?>format='+document.getElementById('<?= $uid ?>-fmt').value;return false;">
        <i class="fa fa-download"></i> <?= Yii::t('StewardshipModule.base', 'Download') ?>
    </a>
</form>
