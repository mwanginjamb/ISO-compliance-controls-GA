<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%clause}}`.
 */
class m250705_101316_create_clause_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%clause}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(250),
            'description' => $this->text(),
            'created_at' => $this->integer(25),
            'updated_at' => $this->integer(25),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%clause}}');
    }
}
