<?php

namespace rest\common\actions;


use rest\common\FormattableDataProvider;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\data\DataFilter;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecordInterface;



class IndexAction extends \yii\rest\IndexAction
{

    public $rowFormatter = null;

    protected function getMainQuery()
    {
        /* @var ActiveRecordInterface $modelClass */
        $modelClass = $this->modelClass;

        $query = $modelClass::find();
        return $query;
    }

    /**
     * Override this to provide a more optimal query for retrieving the scalar value
     * of the given complex query. Make sure this query aggregates and selects just one column,
     * because the result of this function will be called using $countQuery->scalar()
     * @return ActiveQueryInterface
     */
    protected function getTotalCount()
    {
        return $this->getMainQuery()->count();
    }

    /**
     * @return object|FormattableDataProvider|ActiveDataProvider|DataFilter|null
     * @throws InvalidConfigException
     */
    protected function prepareDataProvider()
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $filter = null;
        if ($this->dataFilter !== null) {
            $this->dataFilter = Yii::createObject($this->dataFilter);
            if ($this->dataFilter->load($requestParams)) {
                $filter = $this->dataFilter->build();
                if ($filter === false) {
                    return $this->dataFilter;
                }
            }
        }

        /** @var ActiveQuery $query */
        $query = $this->getMainQuery();

        if (!empty($filter)) {
            $query->andWhere($filter);
        }

        $cfg = [
            'class' => FormattableDataProvider::class,
            'query' => $query,
            'totalCount' => $this->getTotalCount(),
            'pagination' => [
                'params' => $requestParams,
            ],
            'sort' => [
                'params' => $requestParams,
            ],
        ];

        if (is_callable($this->rowFormatter)) {
            $cfg['rowFormatter'] = $this->rowFormatter;
        }

        return Yii::createObject($cfg);
    }
}
