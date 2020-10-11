<?php

class m170109_203526_CRAETE_TABLE_cache extends console\models\BaseMigration
{
    public function up()
    {
        $this->createTable('cache', [
            'id' => $this->char(40)->notNull(),
            'expire' => $this->integer(),
            'data BLOB',
        ]);
        $this->addPrimaryKey('cache_pk1', 'cache', 'id');

        $this->createTable('session', [
            'id' => $this->char(40)->notNull(),
            'expire' => $this->integer(),
            'data BLOB',
        ]);
        $this->addPrimaryKey('session_pk1', 'session', 'id');
    }

    public function down()
    {
        $this->dropTable('cache');
        $this->dropTable('session');
    }
}
