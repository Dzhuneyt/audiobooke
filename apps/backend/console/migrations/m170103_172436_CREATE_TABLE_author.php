<?php

class m170103_172436_CREATE_TABLE_author extends console\models\BaseMigration
{
    public function up()
    {
        $this->createTable('author', [
            ' `id`  int(11) NOT NULL AUTO_INCREMENT',
            'firstname' => $this->string(),
            'lastname' => $this->string(),
            'dob' => $this->integer(4)->comment('Date of birth'),
            'dod' => $this->integer(4)->comment('Date of death'),
            'PRIMARY KEY (`id`, `lastname`, `firstname`)'
        ]);
    }

    public function down()
    {
        $this->dropTable('author');
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
