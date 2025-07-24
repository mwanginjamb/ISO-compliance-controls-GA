<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%clause}}`.
 */
class m250724_085422_add_analyzable_column_to_clause_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%clause}}', 'analyzable', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%clause}}', 'analyzable');
    }
}
