<?php

use yii\db\Migration;

/**
 * Stores API access tokens as SHA-256 hashes with an expiry.
 *
 * Existing tokens are revoked: they were stored in plain text and were
 * displayed on the users page, so they must be treated as compromised.
 */
class m261101_100100_hash_user_access_tokens extends Migration
{
    public function safeUp()
    {
        $this->update('{{%user}}', ['access_token' => null]);
        $this->alterColumn('{{%user}}', 'access_token', $this->string(64)->null());
        $this->addColumn('{{%user}}', 'access_token_expires_at', $this->integer()->null());
        $this->createIndex('{{%idx-user-access_token}}', '{{%user}}', 'access_token', true);
    }

    public function safeDown()
    {
        $this->dropIndex('{{%idx-user-access_token}}', '{{%user}}');
        $this->dropColumn('{{%user}}', 'access_token_expires_at');
        $this->alterColumn('{{%user}}', 'access_token', $this->string(255)->null());
    }
}
