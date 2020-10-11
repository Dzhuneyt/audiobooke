<?php

class m170103_171958_CREATE_TABLE_audiobook extends console\models\BaseMigration
{
    public function up()
    {
        $this->createTable('audiobook', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull()->unique(),
            'description' => $this->text(),
            'language' => $this->string('20'),
            'copyright_year' => $this->integer(5)->defaultValue(0),
            'num_sections' => $this->integer(3)->defaultValue(0),
            'url_zip_file' => $this->string(),
            'totaltimesecs' => $this->integer(),
        ]);
    }

    public function down()
    {
        $this->dropTable('audiobook');
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
