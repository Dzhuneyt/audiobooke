<?php

use console\models\BaseMigration;



/**
 * Class m190218_170215_audiobook_favorites
 */
class m190218_170215_audiobook_favorites extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('audiobook_favorite',
            [
                'id' => $this->primaryKey(),
                'id_book' => $this->integer(11)->notNull(),
                'id_user' => $this->integer(11)->notNull(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP()')
            ]
        );

        $this->addForeignKey(
            'audiobook_favorite_id_book',
            'audiobook_favorite',
            'id_book',
            'audiobook',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'audiobook_favorite_id_user',
            'audiobook_favorite',
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
        $this->dropTable('audiobook_favorite');
    }
}
