<?php

use humhub\components\Migration;

class m260328_000004_election_phases extends Migration
{
    public function up()
    {
        // Add phase columns
        $this->addColumn('election', 'candidacy_expires_at', $this->dateTime()->null()->after('expires_at'));
        $this->addColumn('election', 'voting_expires_at', $this->dateTime()->null()->after('candidacy_expires_at'));

        // Copy existing expires_at to voting_expires_at for backward compat
        $this->update('election', [
            'voting_expires_at' => new \yii\db\Expression('expires_at'),
            'candidacy_expires_at' => new \yii\db\Expression('expires_at'),
        ]);
    }

    public function down()
    {
        $this->dropColumn('election', 'voting_expires_at');
        $this->dropColumn('election', 'candidacy_expires_at');
    }
}
