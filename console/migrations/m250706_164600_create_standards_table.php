<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%standards}}`.
 */
class m250706_164600_create_standards_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%standards}}', [
            'id' => $this->primaryKey(),
            'standard' => $this->string(250)->unique(),
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
        $this->dropTable('{{%standards}}');
    }
}
