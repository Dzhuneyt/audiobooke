<?php

use console\models\BaseMigration;



/**
 * Class m190323_143838_CREATE_TABLE_audiobook_audible
 */
class m190323_143838_CREATE_TABLE_audiobook_audible extends BaseMigration
{
    const TABLE_NAME = 'audiobook_audible';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id_book' => $this->primaryKey(),

            // ISBN length=10. Part of the audible.com URL
            'isbn' => $this->string(10)
                           ->notNull(),
            'audible_url' => $this->string(2000),
            'read_by' => $this->string(),
            'abridged' => $this->boolean(),
            'date_published' => $this->date(),
            'rating' => $this->float(2),
            'rating_count' => $this->integer(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ]);

        // Unique index for SELECT performance
        $this->createIndex(
            'audiobook_audible_isbn_1',
            'audiobook_audible',
            'isbn',
            true
        );

        $this->addForeignKey(
            'audiobook_audible_id_book',
            self::TABLE_NAME,
            'id_book',
            'audiobook',
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
        $this->dropTable(self::TABLE_NAME);
        $this->delete('audiobook', ['type' => 'audible']);
    }
}
