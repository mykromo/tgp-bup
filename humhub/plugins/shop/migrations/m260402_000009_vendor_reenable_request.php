<?php

use humhub\components\Migration;

class m260402_000009_vendor_reenable_request extends Migration
{
    public function up()
    {
        $this->addColumn('shop_vendor', 'reenable_request', $this->text()->null()->after('disabled_by'));
        $this->addColumn('shop_vendor', 'reenable_requested_at', $this->dateTime()->null()->after('reenable_request'));
    }

    public function down()
    {
        $this->dropColumn('shop_vendor', 'reenable_requested_at');
        $this->dropColumn('shop_vendor', 'reenable_request');
    }
}
