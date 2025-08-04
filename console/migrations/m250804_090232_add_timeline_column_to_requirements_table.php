<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%requirements}}`.
 */
class m250804_090232_add_timeline_column_to_requirements_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%requirements}}', 'timeline', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%requirements}}', 'timeline');
    }
}
