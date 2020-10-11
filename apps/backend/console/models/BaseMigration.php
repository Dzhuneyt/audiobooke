<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 2/15/19
 * Time: 2:38 PM
 */

namespace console\models;


use yii\db\Migration;



class BaseMigration extends Migration
{
    public function createTable($table, $columns, $options = null)
    {
        if ($options === null) {
            $options = 'ENGINE InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci';
        }
        parent::createTable($table, $columns, $options);
    }
}
