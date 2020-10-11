<?php
/**
 * Created by PhpStorm.
 * User: dzhuneyt
 * Date: 10.02.19
 * Time: 21:11
 */

namespace rest\versions\v1\controllers;


use common\models\Audiobook;
use rest\common\RestController;
use rest\versions\v1\actions\audiobook\AddToFavoritesAction;
use rest\versions\v1\actions\audiobook\DownloadAudiobookAction;
use rest\versions\v1\actions\audiobook\IndexAction;
use rest\versions\v1\actions\audiobook\ViewAction;
use yii\rest\Controller;
use function foo\func;



class HealthcheckController extends Controller
{
    public function actionIsalive()
    {
        return [];
    }

    protected function guestActions()
    {
        return ['isalive'];
    }
}
