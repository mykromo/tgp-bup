<?php

use humhub\components\Migration;

class m260402_000005_full_shop extends Migration
{
    public function up()
    {
        // Product categories
        $this->createTable('shop_category', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull(),
            'parent_id' => $this->integer()->null(),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'is_active' => $this->boolean()->notNull()->defaultValue(1),
        ]);
        $this->createIndex('idx-shop_cat-slug', 'shop_category', 'slug', true);

        // Product variations (size, color, etc.)
        $this->createTable('shop_product_variant', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'sku' => $this->string(100)->null(),
            'price_adjustment' => $this->decimal(14, 2)->notNull()->defaultValue(0),
            'stock' => $this->integer()->null(),
            'is_active' => $this->boolean()->notNull()->defaultValue(1),
        ]);
        $this->addForeignKey('fk-shop_variant-product', 'shop_product_variant', 'product_id', 'shop_product', 'id', 'CASCADE');

        // Add fields to products
        $this->addColumn('shop_product', 'category_id', $this->integer()->null()->after('vendor_id'));
        $this->addColumn('shop_product', 'location', $this->string(255)->null()->after('image_url'));
        $this->addColumn('shop_product', 'sale_price', $this->decimal(14, 2)->null()->after('price'));
        $this->addColumn('shop_product', 'sale_start', $this->dateTime()->null()->after('sale_price'));
        $this->addColumn('shop_product', 'sale_end', $this->dateTime()->null()->after('sale_start'));

        // Wishlist
        $this->createTable('shop_wishlist', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->null(),
        ]);
        $this->createIndex('idx-shop_wish-unique', 'shop_wishlist', ['user_id', 'product_id'], true);
        $this->addForeignKey('fk-shop_wish-user', 'shop_wishlist', 'user_id', 'user', 'id', 'CASCADE');
        $this->addForeignKey('fk-shop_wish-product', 'shop_wishlist', 'product_id', 'shop_product', 'id', 'CASCADE');

        // Favorite stores
        $this->createTable('shop_favorite_store', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'vendor_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->null(),
        ]);
        $this->createIndex('idx-shop_fav-unique', 'shop_favorite_store', ['user_id', 'vendor_id'], true);
        $this->addForeignKey('fk-shop_fav-user', 'shop_favorite_store', 'user_id', 'user', 'id', 'CASCADE');
        $this->addForeignKey('fk-shop_fav-vendor', 'shop_favorite_store', 'vendor_id', 'shop_vendor', 'id', 'CASCADE');

        // Discount codes
        $this->createTable('shop_discount', [
            'id' => $this->primaryKey(),
            'vendor_id' => $this->integer()->null(),
            'code' => $this->string(50)->notNull(),
            'type' => $this->string(20)->notNull()->defaultValue('percentage'),
            'value' => $this->decimal(14, 2)->notNull(),
            'min_order' => $this->decimal(14, 2)->null(),
            'max_uses' => $this->integer()->null(),
            'used_count' => $this->integer()->notNull()->defaultValue(0),
            'starts_at' => $this->dateTime()->null(),
            'expires_at' => $this->dateTime()->null(),
            'is_active' => $this->boolean()->notNull()->defaultValue(1),
        ]);
        $this->createIndex('idx-shop_disc-code', 'shop_discount', 'code', true);

        // Add variant and discount to order items
        $this->addColumn('shop_order_item', 'variant_id', $this->integer()->null()->after('product_id'));
        $this->addColumn('shop_order_item', 'variant_name', $this->string(255)->null()->after('product_name'));
        $this->addColumn('shop_order', 'discount_code', $this->string(50)->null()->after('notes'));
        $this->addColumn('shop_order', 'discount_amount', $this->decimal(14, 2)->null()->after('discount_code'));

        // Add location to vendor
        $this->addColumn('shop_vendor', 'location', $this->string(255)->null()->after('description'));
    }

    public function down()
    {
        $this->dropColumn('shop_vendor', 'location');
        $this->dropColumn('shop_order', 'discount_amount');
        $this->dropColumn('shop_order', 'discount_code');
        $this->dropColumn('shop_order_item', 'variant_name');
        $this->dropColumn('shop_order_item', 'variant_id');
        $this->dropTable('shop_discount');
        $this->dropTable('shop_favorite_store');
        $this->dropTable('shop_wishlist');
        $this->dropColumn('shop_product', 'sale_end');
        $this->dropColumn('shop_product', 'sale_start');
        $this->dropColumn('shop_product', 'sale_price');
        $this->dropColumn('shop_product', 'location');
        $this->dropColumn('shop_product', 'category_id');
        $this->dropTable('shop_product_variant');
        $this->dropTable('shop_category');
    }
}
