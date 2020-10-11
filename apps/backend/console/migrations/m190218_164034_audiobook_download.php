<?php

use console\models\BaseMigration;



/**
 * Class m190218_164034_audiobook_download
 */
class m190218_164034_audiobook_download extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('audiobook_download', [
            'id' => $this->primaryKey(),
            'id_audiobook' => $this->integer(11)->notNull(),
            'id_user' => $this->integer(11)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP()'),
        ]);
        $this->addForeignKey(
            'audiobook_download_id_audiobook',
            'audiobook_download',
            'id_audiobook',
            'audiobook',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'audiobook_download_id_user',
            'audiobook_download',
            'id_user',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('audiobook_download');
    }

}
