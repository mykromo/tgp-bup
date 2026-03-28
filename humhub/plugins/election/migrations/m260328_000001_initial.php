<?php

use humhub\components\Migration;

class m260328_000001_initial extends Migration
{
    public function up()
    {
        $this->createTable('election', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text()->null(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createTable('election_candidate', [
            'id' => $this->primaryKey(),
            'election_id' => $this->integer()->notNull(),
            'position' => $this->string(100)->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
        ]);

        $this->createTable('election_vote', [
            'id' => $this->primaryKey(),
            'election_id' => $this->integer()->notNull(),
            'candidate_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'position' => $this->string(100)->notNull(),
            'created_at' => $this->dateTime()->null(),
        ]);

        $this->createIndex('idx-election_candidate-election', 'election_candidate', 'election_id');
        $this->createIndex('idx-election_candidate-user', 'election_candidate', 'user_id');
        $this->createIndex('idx-election_vote-election', 'election_vote', 'election_id');
        $this->createIndex('idx-election_vote-candidate', 'election_vote', 'candidate_id');
        $this->createIndex('idx-election_vote-user', 'election_vote', 'user_id');
        // One vote per user per position per election
        $this->createIndex('idx-election_vote-unique', 'election_vote', ['election_id', 'user_id', 'position'], true);

        $this->addForeignKey('fk-election_candidate-election', 'election_candidate', 'election_id', 'election', 'id', 'CASCADE');
        $this->addForeignKey('fk-election_candidate-user', 'election_candidate', 'user_id', 'user', 'id', 'CASCADE');
        $this->addForeignKey('fk-election_vote-election', 'election_vote', 'election_id', 'election', 'id', 'CASCADE');
        $this->addForeignKey('fk-election_vote-candidate', 'election_vote', 'candidate_id', 'election_candidate', 'id', 'CASCADE');
        $this->addForeignKey('fk-election_vote-user', 'election_vote', 'user_id', 'user', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('election_vote');
        $this->dropTable('election_candidate');
        $this->dropTable('election');
    }
}
