<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%sub_clause}}`.
 */
class m250712_134827_add_average_status_column_to_sub_clause_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sub_clause}}', 'average_status', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%sub_clause}}', 'average_status');
    }
}
