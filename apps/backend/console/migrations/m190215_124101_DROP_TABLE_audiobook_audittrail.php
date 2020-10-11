<?php

use console\models\BaseMigration;



/**
 * Class m190215_124101_DROP_TABLE_audiobook_audittrail
 */
class m190215_124101_DROP_TABLE_audiobook_audittrail extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('audiobook_audittrail');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
