<?php
namespace app\controller;

use app\base\application;
use app\base\db\SimpleQueryBuilderMySQL;
use app\helper\cliHelper;

class indexController
{
    public function indexAction()
    {
        cliHelper::e('Main command');
    }

    public function testQueryAction()
    {
        $builder = new SimpleQueryBuilderMySQL();
        $query = $builder
            ->select(['field1', 'f2' => 'field2'])
            ->select('field3')
            ->from('table1')
            ->from(['t2' => 'table2', 'table3'])
            ->where(['field1', 1])
            ->where(['field2', 2, '!='])
            ->where(['field3', '"%search%"', 'LIKE'])
            ->groupBy('field1, field2')
            ->groupBy('field3')
            ->having(['field1', 1])
            ->having(['field2', 2, '!='])
            ->having(['field3', '"%search%"', 'LIKE'])
            ->orderBy('field1 DESC, field2')
            ->orderBy(['field3' => 'DESC'])
            ->limit(100)
            ->offset(10)
            ->build();

        cliHelper::e($query);
    }
}