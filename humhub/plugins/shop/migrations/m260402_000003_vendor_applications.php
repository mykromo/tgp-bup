<?php

use humhub\components\Migration;

class m260402_000003_vendor_applications extends Migration
{
    public function up()
    {
        $this->createTable('shop_vendor', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'shop_name' => $this->string(255)->notNull(),
            'description' => $this->text()->null(),
            'status' => $this->string(20)->notNull()->defaultValue('pending'),
            'rejection_reason' => $this->text()->null(),
            'reviewed_by' => $this->integer()->null(),
            'reviewed_at' => $this->dateTime()->null(),
            'created_at' => $this->dateTime()->null(),
            'updated_at' => $this->dateTime()->null(),
        ]);
        $this->createIndex('idx-shop_vendor-user', 'shop_vendor', 'user_id');
        $this->createIndex('idx-shop_vendor-status', 'shop_vendor', 'status');
        $this->addForeignKey('fk-shop_vendor-user', 'shop_vendor', 'user_id', 'user', 'id', 'CASCADE');

        $this->createTable('shop_vendor_document', [
            'id' => $this->primaryKey(),
            'vendor_id' => $this->integer()->notNull(),
            'document_type' => $this->string(100)->notNull(),
            'file_name' => $this->string(255)->notNull(),
            'file_path' => $this->string(500)->notNull(),
            'file_size' => $this->integer()->null(),
            'notes' => $this->text()->null(),
            'created_at' => $this->dateTime()->null(),
        ]);
        $this->addForeignKey('fk-shop_vendordoc-vendor', 'shop_vendor_document', 'vendor_id', 'shop_vendor', 'id', 'CASCADE');

        // Link products to vendor
        $this->addColumn('shop_product', 'vendor_id', $this->integer()->null()->after('space_id'));
        $this->createIndex('idx-shop_product-vendor', 'shop_product', 'vendor_id');
    }

    public function down()
    {
        $this->dropColumn('shop_product', 'vendor_id');
        $this->dropTable('shop_vendor_document');
        $this->dropTable('shop_vendor');
    }
}
