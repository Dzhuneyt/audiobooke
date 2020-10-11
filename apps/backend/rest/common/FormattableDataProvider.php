<?php

namespace rest\common;


use yii\data\ActiveDataProvider;
use yii\db\Query;



/**
 * A class that can be substituted for ActiveDataProvider
 * inside, e.g. IndexAction.php or any other place where a DataProvider
 * may be necessary. Provides functionality for applying "post processing"
 * for each row through a lambda function
 *
 * Class FormattableDataProvider
 * @package rest\common
 */
class FormattableDataProvider extends ActiveDataProvider
{
    /**
     * @var Query
     */
    public $query;

    /**
     * @var null Callable
     */
    public $rowFormatter = null;

    public function init()
    {
//        if ($this->query) {
//            $this->sql = $this->query->createCommand()->getRawSql();
//        }
        parent::init();
    }

    protected function prepareModels()
    {
        $models = parent::prepareModels();

        if (!is_callable($this->rowFormatter)) {
            return $models;
        }

        foreach ($models as $i => $model) {
            $models[$i] = call_user_func($this->rowFormatter, $model);
        }
        return $models;
    }
}
