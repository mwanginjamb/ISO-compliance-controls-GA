<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%requirements}}`.
 */
class m250730_120413_add_assignee_column_to_requirements_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%requirements}}', 'assignee', $this->string(250));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%requirements}}', 'assignee');
    }
}
