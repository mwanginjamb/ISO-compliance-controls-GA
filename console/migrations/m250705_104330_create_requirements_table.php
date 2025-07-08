<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%requirements}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%sub_clause}}`
 */
class m250705_104330_create_requirements_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%requirements}}', [
            'id' => $this->primaryKey(),
            'description' => $this->text(),
            'status' => $this->integer(),
            'evidence_path' => $this->string(350),
            'gaps' => $this->text(),
            'actions_required' => $this->text(),
            'sub_clause_id' => $this->integer(),
            'created_at' => $this->integer(25),
            'updated_at' => $this->integer(25),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // creates index for column `sub_clause_id`
        $this->createIndex(
            '{{%idx-requirements-sub_clause_id}}',
            '{{%requirements}}',
            'sub_clause_id'
        );

        // add foreign key for table `{{%sub_clause}}`
        $this->addForeignKey(
            '{{%fk-requirements-sub_clause_id}}',
            '{{%requirements}}',
            'sub_clause_id',
            '{{%sub_clause}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%sub_clause}}`
        $this->dropForeignKey(
            '{{%fk-requirements-sub_clause_id}}',
            '{{%requirements}}'
        );

        // drops index for column `sub_clause_id`
        $this->dropIndex(
            '{{%idx-requirements-sub_clause_id}}',
            '{{%requirements}}'
        );

        $this->dropTable('{{%requirements}}');
    }
}
