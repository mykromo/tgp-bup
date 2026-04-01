<?php

use humhub\components\Migration;

class m260401_000002_functional_categories extends Migration
{
    public function up()
    {
        $this->createTable('stewardship_category', [
            'id' => $this->primaryKey(),
            'space_id' => $this->integer()->notNull(),
            'key' => $this->string(50)->notNull(),
            'label' => $this->string(255)->notNull(),
            'is_default' => $this->boolean()->notNull()->defaultValue(0),
            'is_active' => $this->boolean()->notNull()->defaultValue(1),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-stewardship_cat-space', 'stewardship_category', 'space_id');
        $this->createIndex('idx-stewardship_cat-unique', 'stewardship_category', ['space_id', 'key'], true);
        $this->addForeignKey('fk-stewardship_cat-space', 'stewardship_category', 'space_id', 'space', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('stewardship_category');
    }
}
