<?php

use humhub\components\Migration;

class m260328_000006_officer_assignment extends Migration
{
    public function up()
    {
        $this->createTable('election_officer', [
            'id' => $this->primaryKey(),
            'space_id' => $this->integer()->notNull(),
            'position_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'election_id' => $this->integer()->null(),
            'created_at' => $this->dateTime()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-election_officer-space', 'election_officer', 'space_id');
        $this->createIndex('idx-election_officer-unique', 'election_officer', ['space_id', 'position_id'], true);
        $this->addForeignKey('fk-election_officer-space', 'election_officer', 'space_id', 'space', 'id', 'CASCADE');
        $this->addForeignKey('fk-election_officer-position', 'election_officer', 'position_id', 'election_position', 'id', 'CASCADE');
        $this->addForeignKey('fk-election_officer-user', 'election_officer', 'user_id', 'user', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('election_officer');
    }
}
