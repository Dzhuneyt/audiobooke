<?php

class m170304_140310_ALTER_TABLE_cache_MEDIUMBLOB extends console\models\BaseMigration
{
    public function up()
    {
        $this->alterColumn('cache', 'data', 'mediumblob');

    }

    public function down()
    {
        $this->alterColumn('cache', 'data', 'blob');
    }
}
