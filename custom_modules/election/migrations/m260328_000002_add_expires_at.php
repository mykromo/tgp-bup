<?php

use humhub\components\Migration;

class m260328_000002_add_expires_at extends Migration
{
    public function up()
    {
        $this->addColumn('election', 'expires_at', $this->dateTime()->null()->after('status'));
    }

    public function down()
    {
        $this->dropColumn('election', 'expires_at');
    }
}
