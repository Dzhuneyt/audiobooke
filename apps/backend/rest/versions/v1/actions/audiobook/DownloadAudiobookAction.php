<?php
/**
 * Created by PhpStorm.
 * User: dzhuneyt
 * Date: 14.02.19
 * Time: 17:02
 */

namespace rest\versions\v1\actions\audiobook;


use common\models\Audiobook;
use common\models\AudiobookDownload;
use Yii;
use yii\rest\Action;
use yii\web\NotFoundHttpException;



class DownloadAudiobookAction extends Action
{
    public $modelClass = Audiobook::class;

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function run($id)
    {
        $model = Audiobook::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException();
        }

        $downloadLog = new AudiobookDownload([
            'id_audiobook' => $id,
            'id_user' => Yii::$app->user->id,
        ]);
        $downloadLog->save();

        $downloadUrl = $model->url_zip_file;

        return [
            'download_url' => $downloadUrl
        ];
    }

}
