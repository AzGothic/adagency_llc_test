<?php
namespace app\base\db;

use app\base\DbException;

interface driverInterface
{
    /**
     * @param array $config
     * @throws DbException
     */
    public function __construct($config);
}