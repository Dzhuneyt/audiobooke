<?php

class m170103_173625_CREATE_TABLE_audiobook_meta extends console\models\BaseMigration
{
    public function up()
    {
        $this->createTable('audiobook_meta', [
            'id_book' => $this->integer()->notNull(),
            'name' => $this->string(),
            'value' => $this->text(),
            'PRIMARY KEY(`id_book`, `name`)'
        ]);
        $this->addForeignKey('audiobook_meta_id_book', 'audiobook_meta', 'id_book', 'audiobook', 'id', 'CASCADE',
            'CASCADE');
    }

    public function down()
    {
        try {
            $this->dropForeignKey('audiobook_meta_id_book', 'audiobook_meta');
        } catch (Exception $e) {
        }
        $this->dropTable('audiobook_meta');
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
