<?php


class m170106_140014_CREATE_TABLE_audiobook_audittrail extends console\models\BaseMigration
{
    public function up()
    {
        $this->createTable('audiobook_audittrail', [
            'id' => $this->primaryKey(),
            'id_book' => $this->integer()->notNull()->comment('ID of the user who did the action, if logged in'),
            'id_user' => $this->integer(),
            'action' => $this->string(),
            'timestamp' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'ip' => $this->string(),
        ]);
        $this->addForeignKey('audiobook_audittrail_fk1', 'audiobook_audittrail', 'id_book', 'audiobook', 'id',
            'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('audiobook_audittrail');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
