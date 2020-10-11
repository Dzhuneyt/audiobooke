<?php
/**
 * Created by PhpStorm.
 * User: dzhuneyt
 * Date: 14.02.19
 * Time: 17:22
 */

namespace rest\versions\v1\controllers;


use rest\common\RestController;
use rest\versions\v1\actions\user\SsoAction;



class UserController extends RestController
{
    public $modelClass = 'unused';

    public function actions()
    {
        $actions = [];
        $actions['sso'] = [
            'class' => SsoAction::class,
        ];

        $actions['options'] = [
            'class' => 'yii\rest\OptionsAction',
        ];

        return $actions;
    }

    protected function guestActions()
    {
        return array_merge(parent::guestActions(), ['sso']);
    }
}
