<?php

use humhub\components\Migration;

class m260328_000008_calendar_event_ids extends Migration
{
    public function up()
    {
        $this->addColumn('election', 'candidacy_calendar_id', $this->integer()->null()->after('results_posted'));
        $this->addColumn('election', 'voting_calendar_id', $this->integer()->null()->after('candidacy_calendar_id'));
    }

    public function down()
    {
        $this->dropColumn('election', 'voting_calendar_id');
        $this->dropColumn('election', 'candidacy_calendar_id');
    }
}
