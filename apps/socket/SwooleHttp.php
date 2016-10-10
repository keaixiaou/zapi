<?php

namespace socket;

use ZPHP\Core\Db;
use ZPHP\Core\Factory;
use ZPHP\Core\Config;
use ZPHP\Core\Log;
use ZPHP\Core\Swoole;
use ZPHP\Coroutine\Base\CoroutineTask;
use ZPHP\Protocol\Response;
use ZPHP\Socket\Callback\SwooleHttp as ZSwooleHttp;
use ZPHP\Socket\IClient;

class SwooleHttp extends ZSwooleHttp
{

    /**
     * @var Coroutine
     */
    public function onRequest($request, $response)
    {
        ob_start();
        try {
            $mvc = Config::getField('project','mvc');
            $uri = $request->server['path_info'];
            if(strpos($uri,'.')!==false){
                throw new \Exception(403);
            }
            $url_array = explode('/', $uri);
            if(!isset($url_array[3])){
                if(!empty($url_array[1])){
                    $mvc['controller'] = $url_array[1];
                };
                if(!empty($url_array[2])){
                    $mvc['action'] = $url_array[2];
                };
            }else{
                if(!empty($url_array[1])){
                    $mvc['module'] = $url_array[1];
                };
                if(!empty($url_array[2])){
                    $mvc['controller'] = $url_array[2];
                };
                if(!empty($url_array[3])){
                    $mvc['action'] = $url_array[3];
                };
            }
            $controllerClass = Config::get('ctrl_path', 'controllers') . '\\'
                .ucwords($mvc['module']).'\\'.ucwords($mvc['controller']);

            $FController = Factory::getInstance($controllerClass);
            if(empty($FController)){
                throw new \Exception(404);
            }

            if(!empty(Config::getField('project','reload'))&& extension_loaded('runkit')){
                $FController = Factory::reload($controllerClass);
            }
            $controller = clone $FController;
            $action = $mvc['action'];
            if(!method_exists($controller, $action)){
                throw new \Exception(404);
            }
            $controller->method= $action;
            $controller->response = $response;
            $action = 'coroutineApiStart';
            try{
                $generator = call_user_func([$controller, $action]);
                if ($generator instanceof \Generator) {
                    $generator->controller = $controller;
                    $task = new CoroutineTask($generator);
                    $task->work($task->getRoutine());
                    unset($task);
                    Log::write('after request', Log::INFO);
                }
                unset($controller);
            }catch(\Exception $e){
                $response->status(500);
                $msg = DEBUG===true?$e->getMessage():'服务器升空了!';
                echo Swoole::info($msg);
            }


        } catch (\Exception $e) {
            if(intval($e->getMessage())==0){
                Log::write('request:'.json_encode($request));
            }
            $response->status($e->getMessage());
            echo Swoole::info(Response::$HTTP_HEADERS[$e->getMessage()]);
        }
        $result = ob_get_contents();
        ob_end_clean();
//        if(!empty($controller->is_api)){
//            $response->header('Content-Type', 'application/json');
//        }

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
        }
    }


    /**
     * @param $server
     * @param $workerId
     */
    public function onWorkerStop($server, $workerId){
        if(!$server->taskworker) {
            Db::$instance->freeMysqlPool();
        }
        parent::onWorkerStop($server, $workerId);
    }

}
