<?php

use yii\db\Migration;



/**
 * Class m190322_220859_DELETE_empty_librivox_audiobooks
 */
class m190322_220859_DELETE_empty_librivox_audiobooks extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('audiobook', 'url_zip_file=""');
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
        echo "m190322_220859_DELETE_empty_librivox_audiobooks cannot be reverted.\n";

        return false;
    }
    */
}
