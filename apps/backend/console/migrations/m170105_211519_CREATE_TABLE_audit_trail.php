<?php


class m170105_211519_CREATE_TABLE_audit_trail extends console\models\BaseMigration
{
    public function up()
    {
        $this->createTable('audit_trail', [
            'id' => $this->primaryKey(),
            'id_user' => $this->integer()->null()->comment('The ID of the user who did the event, if logged in'),
            'action' => $this->string(100)->notNull(),
            'payload' => $this->text()->comment('Additional information for this event'),
            'current_url' => $this->string()->null()->comment('The URL where the action took place'),
            'timestamp' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'ip' => $this->string()->null(),
        ]);
    }

    public function down()
    {
        $this->dropTable('audit_trail');
    }
}
