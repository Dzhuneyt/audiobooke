<?php

namespace rest\versions\v1\controllers;


use common\models\AudiobookAuthor;
use rest\common\RestController;
use rest\versions\v1\actions\author\LatestAuthorsAction;



class AuthorController extends RestController
{

    public $modelClass = AudiobookAuthor::class;

    public function actions()
    {
        $actions = [];
        $actions['latest']['class'] = LatestAuthorsAction::class;

        return $actions;
    }

    protected function guestActions()
    {
        return array_merge(parent::guestActions(), ['latest']);
    }
}
