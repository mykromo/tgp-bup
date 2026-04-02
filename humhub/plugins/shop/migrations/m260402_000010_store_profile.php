<?php

use humhub\components\Migration;

class m260402_000010_store_profile extends Migration
{
    public function up()
    {
        $this->addColumn('shop_vendor', 'logo_path', $this->string(500)->null()->after('location'));
        $this->addColumn('shop_vendor', 'cover_path', $this->string(500)->null()->after('logo_path'));
        $this->addColumn('shop_vendor', 'tagline', $this->string(255)->null()->after('description'));
    }

    public function down()
    {
        $this->dropColumn('shop_vendor', 'tagline');
        $this->dropColumn('shop_vendor', 'cover_path');
        $this->dropColumn('shop_vendor', 'logo_path');
    }
}
