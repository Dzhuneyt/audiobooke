<?php


class m170103_174308_CREATE_TABLE_audiobook_author extends console\models\BaseMigration
{
    public function up()
    {
        $this->createTable('audiobook_author', [
            'id_book' => $this->integer(),
            'id_author' => $this->integer(),
            'PRIMARY KEY(id_book, id_author)'
        ]);
        $this->addForeignKey('audiobook_author_fk1', 'audiobook_author', 'id_book', 'audiobook', 'id', 'CASCADE',
            'CASCADE');
        $this->addForeignKey('audiobook_author_fk2', 'audiobook_author', 'id_author', 'author', 'id', 'CASCADE',
            'CASCADE');
    }

    public function down()
    {
        $this->dropTable('audiobook_author');
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
