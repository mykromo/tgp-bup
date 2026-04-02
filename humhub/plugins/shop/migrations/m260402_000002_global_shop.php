<?php

use humhub\components\Migration;

class m260402_000002_global_shop extends Migration
{
    public function up()
    {
        // Make space_id nullable for global products/orders
        $this->alterColumn('shop_product', 'space_id', $this->integer()->null());
        $this->alterColumn('shop_order', 'space_id', $this->integer()->null());
        $this->alterColumn('shop_payment_setting', 'space_id', $this->integer()->null());

        // Add a global settings row (space_id = null)
        $exists = (new \yii\db\Query())->from('shop_payment_setting')->where(['space_id' => null])->exists();
        if (!$exists) {
            $this->insert('shop_payment_setting', [
                'space_id' => null,
                'payment_instructions' => "Please send payment via GCash, bank transfer, or cash.\nInclude your Order Number as reference.\nOnce paid, submit your payment reference number.",
                'accepted_methods' => 'GCash,Bank Transfer,Cash',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $this->delete('shop_payment_setting', ['space_id' => null]);
    }
}
