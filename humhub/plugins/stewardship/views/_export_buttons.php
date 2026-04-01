<?php
/* @var $url string base export URL */
?>
<div class="btn-group" style="margin-bottom:10px">
    <a href="<?= $url ?>&format=csv" class="btn btn-default btn-xs"><i class="fa fa-file-text-o"></i> CSV</a>
    <a href="<?= $url ?>&format=xls" class="btn btn-default btn-xs"><i class="fa fa-file-excel-o"></i> XLS</a>
    <a href="<?= $url ?>&format=html" class="btn btn-default btn-xs"><i class="fa fa-file-code-o"></i> HTML</a>
</div>
