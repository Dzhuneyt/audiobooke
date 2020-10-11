<?php

namespace rest\versions\v1\actions\author;


use common\models\AudiobookAuthor;
use common\models\Author;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\Query;



class LatestAuthorsAction extends Action
{


    public function run()
    {
        return (new Query())
            ->select([
                'id' => 'id',
                'dob' => 'dob',
                'dod' => 'dod',
                'name' => 'CONCAT_WS(" ", firstname, lastname)'
            ])
            ->from(Author::tableName() . ' a')
            ->orderBy('id DESC')
            ->limit(20)
            ->all();
    }


}
