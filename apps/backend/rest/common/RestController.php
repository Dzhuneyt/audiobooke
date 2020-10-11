<?php
/**
 * Created by PhpStorm.
 * User: dzhuneyt
 * Date: 10.02.19
 * Time: 21:12
 */

namespace rest\common;


use tecnocen\oauth2server\filters\auth\CompositeAuth;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;



class RestController extends ActiveController
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function beforeAction($action)
    {
//        var_dump(Yii::$app->getRequest()->getMethod());exit;
        if (Yii::$app->getRequest()
                     ->getMethod() === 'OPTIONS') {
            Yii::$app->getResponse()
                     ->getHeaders()
                     ->set('Allow', 'POST GET PUT OPTIONS');
            Yii::$app->end();
        }
        return parent::beforeAction($action);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        if (Yii::$app->getRequest()
                     ->getMethod() === 'OPTIONS') {
            Yii::$app->getResponse()
                     ->getHeaders()
                     ->set('Allow', 'POST GET PUT OPTIONS');
            Yii::$app->end();
        }

        $isGuestAction = in_array($this->action->id, $this->guestActions());
        $hasAuthHeader = Yii::$app->request->headers->has('authorization');

        if (!$isGuestAction || $hasAuthHeader) {
            $behaviors['authenticator'] = [
                'class' => CompositeAuth::class,
                'authMethods' => [
                    ['class' => HttpBearerAuth::class],
                    [
                        'class' => QueryParamAuth::class,
                        'tokenParam' => 'access-token',
                    ],
                ],
            ];
        }

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    protected function guestActions()
    {
        return [];
    }

}
