<?php

namespace rest\common;


use Yii;
use yii\base\Component;
use yii\db\Exception;



class SearchComponent extends Component
{

    /**
     * @param $keyword
     * @return int
     * @throws Exception
     */
    public function save($keyword)
    {
        $idUser = Yii::$app->user->isGuest ? null : Yii::$app->user->id;

        return Yii::$app
            ->db
            ->createCommand()
            ->insert('search', [
                'keyword' => $keyword,
                'id_user' => $idUser,
            ])
            ->execute();
    }

}
