<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tenants}}`.
 */
class m250717_075752_create_tenants_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tenants}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'database_name' => $this->string(100)->unique(),
            'unique_identifier' => $this->string(256)->unique(),
            'created_at' => $this->integer(30),
            'updated_at' => $this->integer(30),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tenants}}');
    }
}
