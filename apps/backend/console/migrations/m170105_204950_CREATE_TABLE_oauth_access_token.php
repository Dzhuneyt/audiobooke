<?php


class m170105_204950_CREATE_TABLE_oauth_access_token extends console\models\BaseMigration
{
    public function up()
    {
        $this->createTable('access_token', [
            'id' => $this->primaryKey(),
            'id_user' => $this->integer()->notNull(),
            'access_token' => $this->string(),
            'created_at' => $this->dateTime()->defaultExpression('NOW()'),
            'expires' => $this->dateTime(),
        ]);
        $this->addForeignKey('access_token_to_user_fk1', 'access_token', 'id_user', 'user', 'id', 'CASCADE', 'CASCADE');

    }

    public function down()
    {
        $this->dropTable('access_token');
    }
}
