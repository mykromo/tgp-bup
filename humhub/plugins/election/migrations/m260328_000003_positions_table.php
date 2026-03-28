<?php

use humhub\components\Migration;

class m260328_000003_positions_table extends Migration
{
    public function up()
    {
        $this->createTable('election_position', [
            'id' => $this->primaryKey(),
            'space_id' => $this->integer()->notNull(),
            'title' => $this->string(255)->notNull(),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-election_position-space', 'election_position', 'space_id');
        $this->addForeignKey('fk-election_position-space', 'election_position', 'space_id', 'space', 'id', 'CASCADE');

        // Add statement/platform field to candidates for self-filing
        $this->addColumn('election_candidate', 'statement', $this->text()->null()->after('user_id'));
    }

    public function down()
    {
        $this->dropColumn('election_candidate', 'statement');
        $this->dropTable('election_position');
    }
}
