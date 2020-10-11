<?php

use yii\db\Migration;



/**
 * Class m190322_215805_ALTER_TABLE_audiobook_ADD_COLUMN_type
 */
class m190322_215805_ALTER_TABLE_audiobook_ADD_COLUMN_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'audiobook',
            'type',
            $this->string(255)->null()->comment("Possible values: librivox, audible")
        );

        // Mark all existing books as Librivox
        $this->update('audiobook', [
            'type' => 'librivox',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('audiobook', 'type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190322_215805_ALTER_TABLE_audiobook_ADD_COLUMN_type cannot be reverted.\n";

        return false;
    }
    */
}
