<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%clause}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%standard}}`
 */
class m250706_165419_add_stardard_id_column_to_clause_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%clause}}', 'standard_id', $this->integer());

        // creates index for column `standard_id`
        $this->createIndex(
            '{{%idx-clause-standard_id}}',
            '{{%clause}}',
            'standard_id'
        );

        // add foreign key for table `{{%standard}}`
        $this->addForeignKey(
            '{{%fk-clause-standard_id}}',
            '{{%clause}}',
            'standard_id',
            '{{%standards}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%standard}}`
        $this->dropForeignKey(
            '{{%fk-clause-standard_id}}',
            '{{%clause}}'
        );

        // drops index for column `standard_id`
        $this->dropIndex(
            '{{%idx-clause-standard_id}}',
            '{{%clause}}'
        );

        $this->dropColumn('{{%clause}}', 'standard_id');
    }
}
