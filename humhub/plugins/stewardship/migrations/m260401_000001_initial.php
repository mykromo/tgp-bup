<?php

use humhub\components\Migration;

class m260401_000001_initial extends Migration
{
    public function up()
    {
        // ── Pillar 1: Fund Accounting ──
        $this->createTable('stewardship_fund', [
            'id' => $this->primaryKey(),
            'space_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'fund_type' => $this->string(30)->notNull(), // unrestricted, temporarily_restricted, permanently_restricted
            'description' => $this->text()->null(),
            'restriction_purpose' => $this->text()->null(),
            'balance' => $this->decimal(14, 2)->notNull()->defaultValue(0),
            'is_active' => $this->boolean()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);
        $this->createIndex('idx-stewardship_fund-space', 'stewardship_fund', 'space_id');
        $this->addForeignKey('fk-stewardship_fund-space', 'stewardship_fund', 'space_id', 'space', 'id', 'CASCADE');

        // Transactions (immutable ledger entries)
        $this->createTable('stewardship_transaction', [
            'id' => $this->primaryKey(),
            'space_id' => $this->integer()->notNull(),
            'fund_id' => $this->integer()->notNull(),
            'grant_id' => $this->integer()->null(),
            'type' => $this->string(20)->notNull(), // income, expense, transfer_in, transfer_out
            'amount' => $this->decimal(14, 2)->notNull(),
            'description' => $this->string(500)->notNull(),
            'reference' => $this->string(100)->null(),
            'functional_category' => $this->string(50)->null(), // program, management, fundraising
            'program_name' => $this->string(255)->null(),
            'is_voided' => $this->boolean()->notNull()->defaultValue(0),
            'voided_by' => $this->integer()->null(),
            'voided_at' => $this->dateTime()->null(),
            'void_reason' => $this->string(500)->null(),
            'transaction_date' => $this->date()->notNull(),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
        ]);
        $this->createIndex('idx-stewardship_txn-space', 'stewardship_transaction', 'space_id');
        $this->createIndex('idx-stewardship_txn-fund', 'stewardship_transaction', 'fund_id');
        $this->createIndex('idx-stewardship_txn-grant', 'stewardship_transaction', 'grant_id');
        $this->createIndex('idx-stewardship_txn-date', 'stewardship_transaction', 'transaction_date');
        $this->addForeignKey('fk-stewardship_txn-space', 'stewardship_transaction', 'space_id', 'space', 'id', 'CASCADE');
        $this->addForeignKey('fk-stewardship_txn-fund', 'stewardship_transaction', 'fund_id', 'stewardship_fund', 'id', 'RESTRICT');

        // ── Pillar 2: Inter-Entity (Due To/Due From) ──
        $this->createTable('stewardship_interentity', [
            'id' => $this->primaryKey(),
            'from_space_id' => $this->integer()->notNull(),
            'to_space_id' => $this->integer()->notNull(),
            'amount' => $this->decimal(14, 2)->notNull(),
            'description' => $this->string(500)->notNull(),
            'status' => $this->string(20)->notNull()->defaultValue('pending'), // pending, settled
            'from_transaction_id' => $this->integer()->null(),
            'to_transaction_id' => $this->integer()->null(),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'settled_at' => $this->dateTime()->null(),
            'settled_by' => $this->integer()->null(),
        ]);
        $this->createIndex('idx-stewardship_ie-from', 'stewardship_interentity', 'from_space_id');
        $this->createIndex('idx-stewardship_ie-to', 'stewardship_interentity', 'to_space_id');
        $this->addForeignKey('fk-stewardship_ie-from', 'stewardship_interentity', 'from_space_id', 'space', 'id', 'CASCADE');
        $this->addForeignKey('fk-stewardship_ie-to', 'stewardship_interentity', 'to_space_id', 'space', 'id', 'CASCADE');

        // ── Pillar 3: Grant Tracking ──
        $this->createTable('stewardship_grant', [
            'id' => $this->primaryKey(),
            'space_id' => $this->integer()->notNull(),
            'fund_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'grantor' => $this->string(255)->null(),
            'amount_awarded' => $this->decimal(14, 2)->notNull(),
            'amount_spent' => $this->decimal(14, 2)->notNull()->defaultValue(0),
            'start_date' => $this->date()->null(),
            'end_date' => $this->date()->null(),
            'status' => $this->string(20)->notNull()->defaultValue('active'), // active, closed, expired
            'reporting_frequency' => $this->string(20)->null(), // monthly, quarterly, annually
            'notes' => $this->text()->null(),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);
        $this->createIndex('idx-stewardship_grant-space', 'stewardship_grant', 'space_id');
        $this->createIndex('idx-stewardship_grant-fund', 'stewardship_grant', 'fund_id');
        $this->addForeignKey('fk-stewardship_grant-space', 'stewardship_grant', 'space_id', 'space', 'id', 'CASCADE');
        $this->addForeignKey('fk-stewardship_grant-fund', 'stewardship_grant', 'fund_id', 'stewardship_fund', 'id', 'RESTRICT');

        // Cost allocations (split one expense across multiple grants/programs)
        $this->createTable('stewardship_allocation', [
            'id' => $this->primaryKey(),
            'transaction_id' => $this->integer()->notNull(),
            'grant_id' => $this->integer()->notNull(),
            'program_name' => $this->string(255)->notNull(),
            'amount' => $this->decimal(14, 2)->notNull(),
            'percentage' => $this->decimal(5, 2)->null(),
            'created_at' => $this->dateTime()->null(),
        ]);
        $this->addForeignKey('fk-stewardship_alloc-txn', 'stewardship_allocation', 'transaction_id', 'stewardship_transaction', 'id', 'CASCADE');
        $this->addForeignKey('fk-stewardship_alloc-grant', 'stewardship_allocation', 'grant_id', 'stewardship_grant', 'id', 'CASCADE');

        // ── Pillar 4: Audit Trail ──
        $this->createTable('stewardship_audit_log', [
            'id' => $this->primaryKey(),
            'space_id' => $this->integer()->notNull(),
            'entity_type' => $this->string(50)->notNull(), // fund, transaction, grant, interentity
            'entity_id' => $this->integer()->notNull(),
            'action' => $this->string(30)->notNull(), // created, updated, voided, settled
            'field_changed' => $this->string(100)->null(),
            'old_value' => $this->text()->null(),
            'new_value' => $this->text()->null(),
            'user_id' => $this->integer()->notNull(),
            'ip_address' => $this->string(45)->null(),
            'created_at' => $this->dateTime()->notNull(),
        ]);
        $this->createIndex('idx-stewardship_audit-space', 'stewardship_audit_log', 'space_id');
        $this->createIndex('idx-stewardship_audit-entity', 'stewardship_audit_log', ['entity_type', 'entity_id']);
        $this->addForeignKey('fk-stewardship_audit-space', 'stewardship_audit_log', 'space_id', 'space', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('stewardship_audit_log');
        $this->dropTable('stewardship_allocation');
        $this->dropTable('stewardship_grant');
        $this->dropTable('stewardship_interentity');
        $this->dropTable('stewardship_transaction');
        $this->dropTable('stewardship_fund');
    }
}
