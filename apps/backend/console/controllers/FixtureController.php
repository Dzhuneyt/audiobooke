<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 3/1/19
 * Time: 1:53 PM
 */

namespace console\controllers;


class FixtureController extends \yii\faker\FixtureController
{

    public function init()
    {
        parent::init();
        $this->namespace = 'fixtures';
        $this->fixtureDataPath = 'fixtures/data';
        $this->templatePath = 'fixtures/templates';
    }

}
