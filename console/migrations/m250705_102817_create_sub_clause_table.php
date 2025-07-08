<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sub_clause}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%clause}}`
 */
class m250705_102817_create_sub_clause_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sub_clause}}', [
            'id' => $this->primaryKey(),
            'number' => $this->string(250),
            'sub_clause' => $this->text(),
            'clause_id' => $this->integer(),
            'created_at' => $this->integer(25),
            'updated_at' => $this->integer(25),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // creates index for column `clause_id`
        $this->createIndex(
            '{{%idx-sub_clause-clause_id}}',
            '{{%sub_clause}}',
            'clause_id'
        );

        // add foreign key for table `{{%clause}}`
        $this->addForeignKey(
            '{{%fk-sub_clause-clause_id}}',
            '{{%sub_clause}}',
            'clause_id',
            '{{%clause}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%clause}}`
        $this->dropForeignKey(
            '{{%fk-sub_clause-clause_id}}',
            '{{%sub_clause}}'
        );

        // drops index for column `clause_id`
        $this->dropIndex(
            '{{%idx-sub_clause-clause_id}}',
            '{{%sub_clause}}'
        );

        $this->dropTable('{{%sub_clause}}');
    }
}
