<?php

use humhub\components\Migration;

class m260328_000007_position_flags extends Migration
{
    public function up()
    {
        $this->addColumn('election_position', 'is_default', $this->boolean()->notNull()->defaultValue(0)->after('sort_order'));
        $this->addColumn('election_position', 'is_active', $this->boolean()->notNull()->defaultValue(1)->after('is_default'));

        // Mark existing seeded positions as default
        $defaults = [
            'Grand Triskelion',
            'Deputy Grand Triskelion',
            'Master Wielder of the Whip',
            'Master Keeper of the Scroll',
            'Master Keeper of the Chest',
        ];
        foreach ($defaults as $title) {
            $this->update('election_position', ['is_default' => 1], ['title' => $title]);
        }
    }

    public function down()
    {
        $this->dropColumn('election_position', 'is_active');
        $this->dropColumn('election_position', 'is_default');
    }
}
