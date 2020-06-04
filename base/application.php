<?php
namespace app\base;

use app\base\db\connector;
use app\base\db\driverInterface;
use app\helper\arrayHelper;

class application
{
    /**
     * Application Config data
     * @var array $config
     */
    public static $config = [];

    /**
     * DB connect
     * @var driverInterface $db
     */
    public static $db;

    /**
     * Composer Loader instance
     * @var object $loader
     */
    public static $loader = null;

    /**
     * Requested Application Controller/Action
     * @var array $route
     */
    public static $route = [
        'controller' => 'index',
        'action'     => 'index',
    ];


    /**
     * Application initialization
     * @param bool $runAction
     * @throws DbException
     */
    public static function init($runAction = true)
    {
        static::initConfig();
        static::initDB();
        static::routeCli();
        if ($runAction) {
            static::runAction();
        }
    }


    /**
     * Simple Config initialization
     * @return void
     */
    protected static function initConfig()
    {
        $configFile      = APP_PATH . 'config/main.php';
        $configLocalFile = APP_PATH . 'config/main-local.php';

        $config = require $configFile;
        if (file_exists($configLocalFile)) {
            $config = arrayHelper::merge($config, require $configLocalFile);
        }

        static::$config = $config;
    }

    /**
     * Simple DB init
     * @return void
     * @throws DbException
     */
    protected static function initDB()
    {
        static::$db = connector::getConnect(static::$config['db']);
    }

    /**
     * Simple CLI Routing
     * @return void
     */
    protected static function routeCli()
    {
        $route = !empty($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : "";
        $routeParts = explode('/', $route);
        static::$route['controller'] = !(empty($routeParts[0])) ? strtolower($routeParts[0]) : 'index';
        static::$route['action']     = !(empty($routeParts[1])) ? strtolower($routeParts[1]) : 'index';
    }

    /**
     * Simple Action runner
     * @return void
     */
    protected static function runAction()
    {
        $controllerClass = 'app\\controller\\' . static::$route['controller'] . 'Controller';
        $controllerInstance = new $controllerClass();
        $controllerInstance->{static::$route['action'] . 'Action'}();
    }
}