<?php

use humhub\components\Migration;

class m260328_000009_date_ranges extends Migration
{
    public function up()
    {
        $this->addColumn('election', 'candidacy_start_at', $this->dateTime()->null()->after('expires_at'));
        $this->addColumn('election', 'voting_start_at', $this->dateTime()->null()->after('candidacy_expires_at'));

        // Backfill: candidacy starts at creation, voting starts at candidacy end
        $this->update('election', [
            'candidacy_start_at' => new \yii\db\Expression('created_at'),
            'voting_start_at' => new \yii\db\Expression('candidacy_expires_at'),
        ]);
    }

    public function down()
    {
        $this->dropColumn('election', 'voting_start_at');
        $this->dropColumn('election', 'candidacy_start_at');
    }
}
