<?php
namespace app\base\db;

use app\base\DbException;

class connector
{
    /**
     * @param $config
     * @return driverInterface
     * @throws DbException
     */
    public static function getConnect($config): driverInterface
    {
        if (empty($config['driver'])) {
            throw new DbException('DB Driver is required!');
        }

        $connectionClass = 'app\\base\\db\\driver\\' . $config['driver'];

        return new $connectionClass($config);
    }
}