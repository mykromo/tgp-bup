<?php

use humhub\components\Migration;

class m260330_000001_initial extends Migration
{
    public function up()
    {
        $this->createTable('reaction', [
            'id' => $this->primaryKey(),
            'object_model' => $this->string(100)->notNull(),
            'object_id' => $this->integer()->notNull(),
            'reaction_type' => $this->string(20)->notNull(),
            'created_by' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->null(),
            'updated_at' => $this->dateTime()->null(),
        ]);

        // One reaction per user per object
        $this->createIndex('idx-reaction-unique', 'reaction', ['object_model', 'object_id', 'created_by'], true);
        $this->createIndex('idx-reaction-object', 'reaction', ['object_model', 'object_id']);
        $this->createIndex('idx-reaction-user', 'reaction', 'created_by');
        $this->addForeignKey('fk-reaction-user', 'reaction', 'created_by', 'user', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('reaction');
    }
}
