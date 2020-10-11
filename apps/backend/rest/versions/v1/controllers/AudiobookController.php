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
use rest\versions\v1\actions\audiobook\Top10Action;
use rest\versions\v1\actions\audiobook\ViewAction;



class AudiobookController extends RestController
{

    public $modelClass = Audiobook::class;

    public function actions()
    {
        $actions = parent::actions();

        $actions['index']['class'] = IndexAction::class;
        $actions['view']['class'] = ViewAction::class;
        $actions['topten']['class'] = Top10Action::class;

        $actions['download'] = [
            'class' => DownloadAudiobookAction::class,
        ];

        $actions['favorite'] = [
            'class' => AddToFavoritesAction::class,
        ];

        // Audiobooks are not editable
        unset($actions['create'], $actions['update'], $actions['delete']);

        return $actions;
    }

    protected function guestActions()
    {
        return array_merge(parent::guestActions(), ['index', 'view', 'topten']);
    }
}
