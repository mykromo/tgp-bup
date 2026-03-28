<?php

use humhub\components\Migration;

class m260328_000005_results_posted extends Migration
{
    public function up()
    {
        $this->addColumn('election', 'results_posted', $this->boolean()->notNull()->defaultValue(0)->after('voting_expires_at'));
    }

    public function down()
    {
        $this->dropColumn('election', 'results_posted');
    }
}
