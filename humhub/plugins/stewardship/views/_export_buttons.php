<?php
/* @var $url string base export URL */
$sep = (strpos($url, '?') !== false) ? '&' : '?';
$uid = 'exp' . crc32($url);
?>
<span style="display:inline-flex; align-items:center; gap:6px">
    <span style="font-size:13px"><?= Yii::t('StewardshipModule.base', 'Export as') ?></span>
    <select id="<?= $uid ?>" class="form-control input-sm" style="width:auto">
        <option value="csv">CSV</option>
        <option value="xlsx">XLSX</option>
        <option value="html">HTML</option>
    </select>
    <button type="button" class="btn btn-default btn-sm" id="<?= $uid ?>btn">
        <i class="fa fa-download"></i> <?= Yii::t('StewardshipModule.base', 'Download') ?>
    </button>
</span>
<script>
document.getElementById('<?= $uid ?>btn').addEventListener('click', function() {
    var fmt = document.getElementById('<?= $uid ?>').value;
    window.open('<?= $url . $sep ?>format=' + fmt, '_blank');
});
</script>
