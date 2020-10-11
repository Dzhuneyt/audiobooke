<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 3/1/19
 * Time: 2:06 PM
 */

namespace fixtures;


use common\models\Author;
use yii\test\ActiveFixture;



class AuthorFixture extends ActiveFixture
{
    public $modelClass = Author::class;

}
