<?php

use humhub\components\Migration;

class m260402_000006_delivery_and_notifications extends Migration
{
    public function up()
    {
        // Buyer delivery addresses
        $this->createTable('shop_address', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'label' => $this->string(100)->notNull(),
            'recipient_name' => $this->string(255)->notNull(),
            'phone' => $this->string(50)->null(),
            'address_line1' => $this->string(255)->notNull(),
            'address_line2' => $this->string(255)->null(),
            'city' => $this->string(100)->notNull(),
            'province' => $this->string(100)->null(),
            'postal_code' => $this->string(20)->null(),
            'country' => $this->string(100)->notNull()->defaultValue('Philippines'),
            'is_default' => $this->boolean()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->null(),
            'updated_at' => $this->dateTime()->null(),
        ]);
        $this->createIndex('idx-shop_addr-user', 'shop_address', 'user_id');
        $this->addForeignKey('fk-shop_addr-user', 'shop_address', 'user_id', 'user', 'id', 'CASCADE');

        // Add delivery info to orders
        $this->addColumn('shop_order', 'address_id', $this->integer()->null()->after('buyer_email'));
        $this->addColumn('shop_order', 'delivery_address', $this->text()->null()->after('address_id'));
        $this->addColumn('shop_order', 'rejection_reason', $this->text()->null()->after('discount_amount'));
        $this->addColumn('shop_order', 'rejected_by', $this->integer()->null()->after('rejection_reason'));
        $this->addColumn('shop_order', 'rejected_at', $this->dateTime()->null()->after('rejected_by'));

        // Add 'rejected' to status options (handled in model)
    }

    public function down()
    {
        $this->dropColumn('shop_order', 'rejected_at');
        $this->dropColumn('shop_order', 'rejected_by');
        $this->dropColumn('shop_order', 'rejection_reason');
        $this->dropColumn('shop_order', 'delivery_address');
        $this->dropColumn('shop_order', 'address_id');
        $this->dropTable('shop_address');
    }
}
