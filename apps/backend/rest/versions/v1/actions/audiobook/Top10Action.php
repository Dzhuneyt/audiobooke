<?php

namespace rest\versions\v1\actions\audiobook;


use common\models\Audiobook;
use common\models\AudiobookCover;
use common\models\AudiobookDownload;
use common\models\AudiobookFavorite;
use Yii;
use yii\base\Action;
use yii\base\Exception;
use yii\db\Query;



class Top10Action extends Action
{
    public function run()
    {
        $top = (new Query())
            ->select('audiobook.id, audiobook.title, audiobook.description, cover.url as cover_url')
            ->from(
                [
                    'topten' => (new Query())
                        ->select('id_audiobook as id, COUNT(id_user) as downloads')
                        ->from(AudiobookDownload::tableName())
                        ->groupBy('id_audiobook')
                        ->orderBy('COUNT(id_user) DESC')
                ]
            )
            ->leftJoin(Audiobook::tableName() . ' audiobook', 'audiobook.id=topten.id')
            ->leftJoin(AudiobookCover::tableName() . ' cover', 'audiobook.id=cover.id_book');

        // Execute query
        $top10 = $top->all();

        // Format data
        foreach ($top10 as $key => $value) {
            $top10[$key]['description'] = strip_tags($value['description']);
            $top10[$key]['cover_url'] = str_ireplace('http://', 'https://', $value['cover_url']);
        }

        return [
            'topten' => $top10,
        ];
    }

}
