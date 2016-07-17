<?php
/**
 * author: shenzhe
 * Date: 13-6-17
 * 初始化框架相关信息
 */
namespace ZPHP;
use ZPHP\Platform\Linux;
use ZPHP\Platform\Windows;
use ZPHP\Protocol\Response;
use ZPHP\View,
    ZPHP\Core\Config,
    ZPHP\Core\Log,
    ZPHP\Common\Debug,
    ZPHP\Common\Formater;

class ZPHP
{
    /**
     * 项目目录
     * @var string
     */
    private static $rootPath;
    /**
     * 配置目录
     * @var string
     */
    private static $configPath = 'default';
    private static $appPath = 'apps';
    private static $zPath;
    private static $libPath='lib';
    private static $classPath = array();
    private static $os;
    private static $server_pid;
    private static $server_file;


    public static function setOs($os){
        self::$os = $os;
    }

    public static function getOs(){
        return self::$os;
    }

    public static function getRootPath()
    {
        return self::$rootPath;
    }


    public static function setRootPath($rootPath)
    {
        self::$rootPath = $rootPath;
    }

    public static function getConfigPath()
    {
        $dir = self::getRootPath() . DS . 'config' . DS . self::$configPath;
        if (\is_dir($dir)) {
            return $dir;
        }
        return self::getRootPath() . DS . 'config' . DS . 'default';
    }

    public static function setConfigPath($path)
    {
        self::$configPath = $path;
    }

    public static function getAppPath()
    {
        return self::$appPath;
    }

    public static function setAppPath($path)
    {
        self::$appPath = $path;
    }

    public static function getZPath()
    {
        return self::$zPath;
    }

    public static function getLibPath()
    {
        return self::$libPath;
    }

    final public static function autoLoader($class)
    {

        if(isset(self::$classPath[$class])) {
            require self::$classPath[$class];
            return;
        }
        $baseClasspath = \str_replace('\\', DS, $class) . '.php';
        $libs = array(
            self::$rootPath . DS . self::$appPath,
            self::$zPath
        );
        if(is_array(self::$libPath)) {
            $libs = array_merge($libs, self::$libPath);
        } else {
            $libs[] = self::$libPath;
        }
        foreach ($libs as $lib) {
            $classpath = $lib . DS . $baseClasspath;
            if (\is_file($classpath)) {
                self::$classPath[$class] = $classpath;
                require "{$classpath}";
                return;
            }
        }
    }

    final public static function exceptionHandler($exception)
    {
        echo Formater::exception($exception);
//        return Response::display(Formater::exception($exception));
    }

    final public static function fatalHandler()
    {
        $error = \error_get_last();
        Log::write('error:'.json_encode($error));
        if(empty($error)) {
            return;
        }
        if(!in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
            return;
        }
        Response::status('200');
        echo Formater::fatal($error);
//        return Response::display(Formater::fatal($error));
    }

    /**
     * @param $rootPath
     * @param bool $run
     * @param null $configPath
     * @return \ZPHP\Server\IServer
     * @throws \Exception
     */
    public static function run($rootPath, $run=true, $configPath=null)
    {
        global $argv;
        if(empty($argv[1])||!in_array($argv[1],['stop','start','reload','restart'])){
            echo "=====================================================\n";
            echo "Usage: php {$argv[0]} start|stop|reload|restart\n";
            echo "=====================================================\n";
            exit;
        }else {

            defined('DS') || define('DS', DIRECTORY_SEPARATOR);
            self::$zPath = $rootPath;
            self::setRootPath($rootPath);
            if (empty($configPath)) {
                $configPath = 'socket_http';
//            if (!empty($_SERVER['HTTP_HOST'])) {
//                $configPath = \str_replace(':', '_', $_SERVER['HTTP_HOST']);
//            } elseif (!empty($_SERVER['argv'][1])) {
//                $configPath = $_SERVER['argv'][1];
//            }
            }
            self::setConfigPath($configPath);

            \spl_autoload_register(__CLASS__ . '::autoLoader');
            $config_path = self::getConfigPath();
//            Config::load($config_path);
            Config::load($config_path);
            //设置项目lib目录
            self::$libPath = Config::get('lib_path', self::$zPath . DS . 'lib');
            if ($run && Config::getField('project', 'debug_mode', 0)) {
                Debug::start();
            }
            //设置app目录
            $appPath = Config::get('app_path', self::$appPath);
            self::setAppPath($appPath);
            $eh = Config::getField('project', 'exception_handler', __CLASS__ . '::exceptionHandler');
            \set_exception_handler($eh);
//            \register_shutdown_function(Config::getField('project', 'fatal_handler', __CLASS__ . '::fatalHandler'));

            if (Config::getField('project', 'error_handler')) {
                \set_error_handler(Config::getField('project', 'error_handler'));
            }

            $timeZone = Config::get('time_zone', 'Asia/Shanghai');
            \date_default_timezone_set($timeZone);

            if (PHP_OS == 'WINNT')
            {
                self::setOs(new Windows());
            }else{
                self::setOs(new Linux());
            }
            self::$server_file = Config::getField('project', 'pid_path').DS.Config::get('project_name').'_master.pid';
            if(!file_exists(self::$server_file)){
                self::$server_pid = 0;
            }else{
                self::$server_pid = file_get_contents(self::$server_file);
            }
            self::doCommand($argv[1],$run);
        }

    }


    //执行命令
    protected static function doCommand($argv ,$run){

        if ($argv == 'start') {
            self::start($run);
        }else if ($argv=='stop'){
            self::stop();
            exit( 'Service stop success!\n');
        }else if ($argv =='restart'){
            self::stop();
            echo "Service stop success!\nService is starting...\n";
            sleep(2);
            self::start($run);
        }else if ($argv=='reload'){
            self::reload();

        }


    }


    protected static function start($run){
        if(!file_exists(self::$server_file)){
            self::$server_pid = 0;
        }else{
            self::$server_pid = file_get_contents(self::$server_file);
        }
        if(empty(self::$server_pid)){
            $serverMode = Config::get('server_mode', 'Http');
            //寻找server的socket适配器
            $service = Server\Factory::getInstance($serverMode);

            if ($run) {
                echo ( "Service startting success!\n");
                $service->run();
            } else {
                return $service;
            }
        }else{
            echo ( "Service already started!\n");
        }

        if ($run && Config::getField('project', 'debug_mode', 0)) {
            Debug::end();
        }
    }

    protected static function stop(){
        if(empty(self::$server_pid)){
            echo ("Service has shut down!\n");
        }else{
            if(true===self::getOs()->kill(self::$server_pid, SIGTERM)){
                unlink(self::$server_file);
            };
        }

    }

    protected static function reload(){
        if (empty(self::$server_pid))
        {
            exit("Server is not running");
        }
        self::$os->kill(self::$server_pid, SIGUSR1);
        exit;
    }



}
