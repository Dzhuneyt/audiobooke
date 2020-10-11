<?php


class m170105_181358_CREATE_TABLE_rating extends console\models\BaseMigration
{
    const FK1 = 'audiobook_rating_to_audiobook_fk1';
    const FK2 = 'audiobook_rating_to_user_fk1';

    public function up()
    {
        $this->execute('ALTER TABLE user ENGINE = INNODB;');

        $this->createTable('audiobook_rating', [
            'id_book' => $this->integer()->notNull(),
            'id_user' => $this->integer()->notNull(),
            'rating' => $this->integer(1)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'last_ip' => $this->string(),
        ]);

        // On audiobook delete, delete the ratings for it
        $this->addForeignKey(self::FK1, 'audiobook_rating', 'id_book', 'audiobook', 'id', 'CASCADE', 'CASCADE');

        // On user delete, delete his ratings
        $this->addForeignKey(self::FK2, 'audiobook_rating', 'id_user', 'user', 'id', 'CASCADE', 'CASCADE');

    }

    public function down()
    {
        $this->dropForeignKey(self::FK1, 'audiobook_rating');
        $this->dropForeignKey(self::FK2, 'audiobook_rating');
        $this->dropTable('audiobook_rating');
    }

}
