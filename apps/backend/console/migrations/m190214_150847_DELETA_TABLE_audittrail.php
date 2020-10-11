<?php

/**
 * Class m190214_150847_DELETA_TABLE_audittrail
 */
class m190214_150847_DELETA_TABLE_audittrail extends console\models\BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('audit_trail');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }

}
