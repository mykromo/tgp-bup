<?php

use humhub\components\Migration;

class m260402_000001_initial extends Migration
{
    public function up()
    {
        // Products
        $this->createTable('shop_product', [
            'id' => $this->primaryKey(),
            'space_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text()->null(),
            'price' => $this->decimal(14, 2)->notNull(),
            'currency' => $this->string(10)->notNull()->defaultValue('PHP'),
            'stock' => $this->integer()->null(),
            'image_url' => $this->string(500)->null(),
            'is_active' => $this->boolean()->notNull()->defaultValue(1),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);
        $this->createIndex('idx-shop_product-space', 'shop_product', 'space_id');
        $this->addForeignKey('fk-shop_product-space', 'shop_product', 'space_id', 'space', 'id', 'CASCADE');

        // Orders
        $this->createTable('shop_order', [
            'id' => $this->primaryKey(),
            'space_id' => $this->integer()->notNull(),
            'order_number' => $this->string(30)->notNull(),
            'user_id' => $this->integer()->notNull(),
            'status' => $this->string(20)->notNull()->defaultValue('pending'),
            'total_amount' => $this->decimal(14, 2)->notNull(),
            'currency' => $this->string(10)->notNull()->defaultValue('PHP'),
            'payment_reference' => $this->string(255)->null(),
            'payment_method' => $this->string(100)->null(),
            'payment_date' => $this->dateTime()->null(),
            'payment_verified' => $this->boolean()->notNull()->defaultValue(0),
            'verified_by' => $this->integer()->null(),
            'verified_at' => $this->dateTime()->null(),
            'notes' => $this->text()->null(),
            'buyer_name' => $this->string(255)->null(),
            'buyer_email' => $this->string(255)->null(),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
        ]);
        $this->createIndex('idx-shop_order-space', 'shop_order', 'space_id');
        $this->createIndex('idx-shop_order-user', 'shop_order', 'user_id');
        $this->createIndex('idx-shop_order-number', 'shop_order', 'order_number', true);
        $this->addForeignKey('fk-shop_order-space', 'shop_order', 'space_id', 'space', 'id', 'CASCADE');
        $this->addForeignKey('fk-shop_order-user', 'shop_order', 'user_id', 'user', 'id', 'CASCADE');

        // Order items
        $this->createTable('shop_order_item', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'product_name' => $this->string(255)->notNull(),
            'quantity' => $this->integer()->notNull()->defaultValue(1),
            'unit_price' => $this->decimal(14, 2)->notNull(),
            'total_price' => $this->decimal(14, 2)->notNull(),
        ]);
        $this->addForeignKey('fk-shop_item-order', 'shop_order_item', 'order_id', 'shop_order', 'id', 'CASCADE');
        $this->addForeignKey('fk-shop_item-product', 'shop_order_item', 'product_id', 'shop_product', 'id', 'RESTRICT');

        // Payment settings per space
        $this->createTable('shop_payment_setting', [
            'id' => $this->primaryKey(),
            'space_id' => $this->integer()->notNull(),
            'payment_instructions' => $this->text()->null(),
            'accepted_methods' => $this->string(500)->null(),
            'created_at' => $this->dateTime()->null(),
            'updated_at' => $this->dateTime()->null(),
        ]);
        $this->createIndex('idx-shop_paysetting-space', 'shop_payment_setting', 'space_id', true);
        $this->addForeignKey('fk-shop_paysetting-space', 'shop_payment_setting', 'space_id', 'space', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('shop_payment_setting');
        $this->dropTable('shop_order_item');
        $this->dropTable('shop_order');
        $this->dropTable('shop_product');
    }
}
