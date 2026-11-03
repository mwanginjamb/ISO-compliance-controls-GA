<?php

use yii\db\Migration;

/**
 * Adds the assessment period and the source standard a gap analysis was cloned from.
 */
class m261101_100200_add_period_and_cloned_from_to_standards_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%standards}}', 'period', $this->string(50)->null());
        $this->addColumn('{{%standards}}', 'cloned_from_id', $this->integer()->null());

        $this->createIndex('{{%idx-standards-cloned_from_id}}', '{{%standards}}', 'cloned_from_id');
        $this->addForeignKey(
            '{{%fk-standards-cloned_from_id}}',
            '{{%standards}}',
            'cloned_from_id',
            '{{%standards}}',
            'id',
            'SET NULL'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-standards-cloned_from_id}}', '{{%standards}}');
        $this->dropIndex('{{%idx-standards-cloned_from_id}}', '{{%standards}}');
        $this->dropColumn('{{%standards}}', 'cloned_from_id');
        $this->dropColumn('{{%standards}}', 'period');
    }
}
