<?php

namespace socket;

use ZPHP\Controller\Controller;
use ZPHP\Core\App;
use ZPHP\Core\Db;
use ZPHP\Core\Dispatcher;
use ZPHP\Core\Factory;
use ZPHP\Core\Config;
use ZPHP\Core\Log;
use ZPHP\Core\Request;
use ZPHP\Core\Route;
use ZPHP\Core\Swoole;
use ZPHP\Coroutine\Base\CoroutineTask;
use ZPHP\Protocol\Response;
use ZPHP\Session\Session;
use ZPHP\Socket\Callback\SwooleHttp as ZSwooleHttp;
use ZPHP\Socket\IClient;

class SwooleHttp extends ZSwooleHttp
{

    /**
     * @var Dispatcher $dispatcher
     */
    protected $dispatcher;
    /**
     * @var CoroutineTask $coroutineTask
     */
    protected $coroutineTask;

    /**
     * @var Request $requestDeal;
     */
    protected $requestDeal;
    /**
     * @var Coroutine
     */
    public function onRequest($request, $response)
    {
        ob_start();
        try {

            if(strpos($request->server['path_info'],'.')!==false){
                throw new \Exception(403);
            }
            $this->requestDeal->init($request, $response);
            $httpResult = $this->dispatcher->distribute($this->requestDeal);
            if($httpResult!=='NULL') {
                if(!is_array($httpResult)){
                    $httpResult = strval($httpResult);
                }
                $response->end($httpResult);
            }
            return 0;
        } catch (\Exception $e) {
            if(intval($e->getMessage())==0){
                Log::write('Exception error. Request:'.json_encode($request));
            }
            $response->status($e->getMessage());
            echo Swoole::info(Response::$HTTP_HEADERS[$e->getMessage()]);
        }
        $result = ob_get_contents();
        ob_end_clean();

        if(!empty($result)) {
            $response->end($result);
        }
    }



    /**
     * @param $server
     * @param $workerId
     * @throws \Exception
     */
    public function onWorkerStart($server, $workerId)
    {
        parent::onWorkerStart($server, $workerId);
        $common = Config::get('common_file');
        if(!empty($common)){
            require ROOTPATH.$common;
        }
        if (!$server->taskworker) {//worker进程启动协程调度器
            Db::getInstance()->initMysqlPool($workerId);
            Db::getInstance()->initRedisPool($workerId);
            App::init(Factory::getInstance(\ZPHP\Core\DI::class));
            Route::init();
            $this->coroutineTask = Factory::getInstance(\ZPHP\Coroutine\Base\CoroutineTask::class);
            $this->dispatcher = Factory::getInstance(\ZPHP\Core\Dispatcher::class);
            $this->requestDeal = Factory::getInstance(\ZPHP\Core\Request::class, $this->coroutineTask);
        }
    }


    /**
     * @param $server
     * @param $workerId
     */
    public function onWorkerStop($server, $workerId){
        if(!$server->taskworker) {
            Db::getInstance()->freeMysqlPool();
            Db::getInstance()->freeRedisPool();
        }
        parent::onWorkerStop($server, $workerId);
    }

}
