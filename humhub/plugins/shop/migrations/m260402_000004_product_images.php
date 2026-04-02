<?php

use humhub\components\Migration;

class m260402_000004_product_images extends Migration
{
    public function up()
    {
        $this->createTable('shop_product_image', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'file_name' => $this->string(255)->notNull(),
            'file_path' => $this->string(500)->notNull(),
            'file_size' => $this->integer()->null(),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->null(),
        ]);
        $this->addForeignKey('fk-shop_pimg-product', 'shop_product_image', 'product_id', 'shop_product', 'id', 'CASCADE');
        $this->createIndex('idx-shop_pimg-product', 'shop_product_image', 'product_id');
    }

    public function down()
    {
        $this->dropTable('shop_product_image');
    }
}
