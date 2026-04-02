<?php

use humhub\components\Migration;

class m260402_000007_order_updates extends Migration
{
    public function up()
    {
        // Order update/cancel requests
        $this->createTable('shop_order_request', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'type' => $this->string(20)->notNull(), // update, cancel
            'status' => $this->string(20)->notNull()->defaultValue('pending'), // pending, approved, rejected
            'details' => $this->text()->null(),
            'new_address_id' => $this->integer()->null(),
            'new_quantity' => $this->integer()->null(),
            'seller_response' => $this->text()->null(),
            'responded_at' => $this->dateTime()->null(),
            'created_at' => $this->dateTime()->null(),
        ]);
        $this->addForeignKey('fk-shop_oreq-order', 'shop_order_request', 'order_id', 'shop_order', 'id', 'CASCADE');
        $this->addForeignKey('fk-shop_oreq-user', 'shop_order_request', 'user_id', 'user', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('shop_order_request');
    }
}
