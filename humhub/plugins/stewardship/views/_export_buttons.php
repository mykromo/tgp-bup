<?php
/* @var $url string base export URL */
$sep = (strpos($url, '?') !== false) ? '&' : '?';
$baseUrl = $url . $sep . 'format=';
?>
<span style="display:inline-flex; align-items:center; gap:6px">
    <span style="font-size:13px"><?= Yii::t('StewardshipModule.base', 'Export as') ?></span>
    <select class="form-control input-sm export-format-select" style="width:auto">
        <option value="csv">CSV</option>
        <option value="xlsx">XLSX</option>
        <option value="html">HTML</option>
    </select>
    <a href="<?= $baseUrl ?>csv" class="btn btn-default btn-sm export-download-btn" data-pjax-prevent data-base-url="<?= $baseUrl ?>">
        <i class="fa fa-download"></i> <?= Yii::t('StewardshipModule.base', 'Download') ?>
    </a>
</span>
<?php
$this->registerJs("
jQuery(document).on('change', '.export-format-select', function() {
    var btn = jQuery(this).siblings('.export-download-btn');
    btn.attr('href', btn.data('base-url') + jQuery(this).val());
});
", \yii\web\View::POS_READY, 'export-format-js');
?>
