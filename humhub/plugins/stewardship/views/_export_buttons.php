<?php
/* @var $url string base export URL */
$sep = (strpos($url, '?') !== false) ? '&' : '?';
?>
<a href="<?= $url . $sep ?>format=csv" class="btn btn-default btn-sm"><i class="fa fa-file-text-o"></i> CSV</a>
<a href="<?= $url . $sep ?>format=xls" class="btn btn-default btn-sm"><i class="fa fa-file-excel-o"></i> XLS</a>
<a href="<?= $url . $sep ?>format=html" class="btn btn-default btn-sm"><i class="fa fa-file-code-o"></i> HTML</a>
