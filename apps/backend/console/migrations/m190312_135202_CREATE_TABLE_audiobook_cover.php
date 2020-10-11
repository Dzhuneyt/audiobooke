<?php

use common\models\AudiobookMeta;
use console\models\BaseMigration;
use yii\db\Query;



class m190312_135202_CREATE_TABLE_audiobook_cover extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->getDb()
                 ->getTableSchema('audiobook_cover') !== null) {
            $this->dropTable('audiobook_cover');
        }

        $this->createTable('audiobook_cover', [
            'id' => $this->primaryKey(),
            'id_book' => $this->integer(),
            'url' => $this->string(2000)
        ]);
        $this->addForeignKey(
            'audiobook_cover_audiobook_fk1',
            'audiobook_cover',
            'id_book',
            'audiobook',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->moveDataBetweenTables();

        // @TODO in a future migration, delete all rows from "audiobook_meta" where "name=cover_url"
    }

    private function moveDataBetweenTables()
    {
        $existingCovers = (new Query())
            ->select('id_book, value')
            ->from(AudiobookMeta::tableName())
            ->where(['name' => 'cover_url'])
            ->all();

        $toInsert = [];
        foreach ($existingCovers as $existingCover) {
            $idBook = $existingCover['id_book'];
            $coverUrl = $existingCover['value'];

            $toInsert[] = [$idBook, $coverUrl];
        }

        $this->db->createCommand()
                 ->batchInsert('audiobook_cover', ['id_book', 'url'], $toInsert)
                 ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190312_135202_CREATE_TABLE_audiobook_cover cannot be reverted.\n";

        return false;
    }

}
