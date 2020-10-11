<?php

use yii\db\Migration;



/**
 * Class m190301_171000_CREATE_TABLE_searches
 */
class m190301_171000_CREATE_TABLE_searches extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('search', [
            'id' => $this->primaryKey(),
            'keyword' => $this->string(255)->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP()'),
            'id_user' => $this->integer()->null(),
        ]);
        $this->addForeignKey('search_to_user_id', 'search', 'id_user', 'user', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('search');
    }

}
