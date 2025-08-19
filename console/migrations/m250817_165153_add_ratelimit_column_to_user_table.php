<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user}}`.
 */
class m250817_165153_add_ratelimit_column_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'allowance', $this->integer());
        $this->addColumn('{{%user}}', 'allowance_updated_at', $this->integer(26));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'allowance');
        $this->dropColumn('{{%user}}', 'allowance_updated_at');
    }
}
