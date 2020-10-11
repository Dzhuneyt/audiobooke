<?php

use console\models\BaseMigration;



/**
 * Class m190216_113703_RENAME_TABLE_auth_TO_user_sso_provider
 */
class m190216_113703_RENAME_TABLE_auth_TO_user_sso_provider extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable('auth', 'user_sso_provider');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameTable('user_sso_provider', 'auth');
    }

}
