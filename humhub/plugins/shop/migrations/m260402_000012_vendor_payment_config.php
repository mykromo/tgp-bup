<?php

use humhub\components\Migration;

class m260402_000012_vendor_payment_config extends Migration
{
    public function up()
    {
        $this->addColumn('shop_vendor', 'payment_instructions', $this->text()->null()->after('tagline'));
        $this->addColumn('shop_vendor', 'accepted_methods', $this->string(500)->null()->after('payment_instructions'));
    }

    public function down()
    {
        $this->dropColumn('shop_vendor', 'accepted_methods');
        $this->dropColumn('shop_vendor', 'payment_instructions');
    }
}
