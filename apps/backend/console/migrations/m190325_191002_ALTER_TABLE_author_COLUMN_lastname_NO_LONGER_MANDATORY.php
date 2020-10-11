<?php

use yii\db\Migration;



/**
 * Class m190325_191002_ALTER_TABLE_author_COLUMN_lastname_NO_LONGER_MANDATORY
 */
class m190325_191002_ALTER_TABLE_author_COLUMN_lastname_NO_LONGER_MANDATORY extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE author DROP PRIMARY KEY, ADD PRIMARY KEY(id)');
        $this->alterColumn(
            'author',
            'lastname',
            $this->string()
                 ->null()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190325_191002_ALTER_TABLE_author_COLUMN_lastname_NO_LONGER_MANDATORY cannot be reverted.\n";

        return false;
    }
    */
}
