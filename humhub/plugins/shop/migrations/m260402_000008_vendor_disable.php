<?php

use humhub\components\Migration;

class m260402_000008_vendor_disable extends Migration
{
    public function up()
    {
        $this->addColumn('shop_vendor', 'disabled_reason', $this->text()->null()->after('rejection_reason'));
        $this->addColumn('shop_vendor', 'disabled_at', $this->dateTime()->null()->after('disabled_reason'));
        $this->addColumn('shop_vendor', 'disabled_by', $this->integer()->null()->after('disabled_at'));
    }

    public function down()
    {
        $this->dropColumn('shop_vendor', 'disabled_by');
        $this->dropColumn('shop_vendor', 'disabled_at');
        $this->dropColumn('shop_vendor', 'disabled_reason');
    }
}
