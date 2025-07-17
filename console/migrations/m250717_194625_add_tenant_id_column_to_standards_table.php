<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%standards}}`.
 */
class m250717_194625_add_tenant_id_column_to_standards_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%standards}}', 'tenant_id', $this->integer()->notNull()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%standards}}', 'tenant_id');
    }
}
