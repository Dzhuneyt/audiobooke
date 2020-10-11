<?php
/**
 * Created by PhpStorm.
 * User: dzhuneyt
 * Date: 18.02.19
 * Time: 19:14
 */

namespace rest\versions\v1\actions\audiobook;


use common\models\Audiobook;
use common\models\AudiobookFavorite;
use Yii;
use yii\base\Exception;
use yii\rest\Action;



class AddToFavoritesAction extends Action
{
    public $modelClass = Audiobook::class;

    public function run($id)
    {
        $model = AudiobookFavorite::findOne([
            'id_book' => $id,
            'id_user' => Yii::$app->user->id,
        ]);
        if (!$model) {
            $model = new AudiobookFavorite([
                'id_user' => Yii::$app->user->id,
                'id_book' => $id
            ]);
        }
        if (!$model->save()) {
            Yii::error($model->getErrors());
            throw new Exception('Unable to add to favorites');
        }

        return [
            'success' => true
        ];

    }

}
