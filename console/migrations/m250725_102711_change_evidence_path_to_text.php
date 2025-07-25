<?php

use yii\db\Migration;

class m250725_102711_change_evidence_path_to_text extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%requirements}}', 'evidence_path', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%requirements}}', 'evidence_path', $this->string(350));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250725_102711_change_evidence_path_to_text cannot be reverted.\n";

        return false;
    }
    */
}
