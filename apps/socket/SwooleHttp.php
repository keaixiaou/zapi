<?php

namespace socket;

use ZPHP\Core\App;
use ZPHP\Core\Db;
use ZPHP\Core\Factory;
use ZPHP\Core\Config;
use ZPHP\Core\Log;
use ZPHP\Core\Swoole;
use ZPHP\Coroutine\Base\CoroutineTask;
use ZPHP\Protocol\Response;
use ZPHP\Route\Route;
use ZPHP\Session\Session;
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
            $this->doBeforeStart($request, $response);

            $uri = $request->server['path_info'];
            if(strpos($uri,'.')!==false){
                throw new \Exception(403);
            }
            $mvc = \ZPHP\Core\Route::parse($uri, $request->server['request_method']);
            if(!is_array($mvc)){
                $response->end(call_user_func($mvc));
                return 0;
            }

            $controllerClass = Config::get('ctrl_path', 'controllers') . '\\'
                .$mvc['module'].'\\'.$mvc['controller'];

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

            $controller->module = $mvc['module'];
            $controller->controller = $mvc['controller'];
            $controller->method= $action;
            $controller->request = $request;
            $controller->response = $response;
            $action = 'coroutine'.(!empty($controller->isApi)?'Api':'Html').'Start';
            try{
                $generator = call_user_func([$controller, $action]);
                if ($generator instanceof \Generator) {
                    $generator->controller = $controller;
                    $task = new CoroutineTask($generator);
                    $task->work($task->getRoutine());
                    unset($task);
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

        if(!empty($result)) {
            $response->end($result);
        }
    }


    /**
     * 处理请求前的一些操作
     * @param $request
     * @param $response
     * @throws \Exception
     */
    protected function doBeforeStart($request, $response){
        //获取session
        if(!empty(Config::getField('session','enable'))) {
            $_SESSION = Session::get($request, $response);
        }
        //传入请求参数
        if(!empty($request->cookie))$_COOKIE = $request->cookie;
        if(!empty($request->post))$_POST = $request->post??[];
        if(!empty($request->get))$_GET = $request->get??[];
        $methodType = $request->server['request_method'];
        $_REQUEST = $methodType=='GET'?array_merge($_GET, $_POST):array_merge($_POST, $_GET);
        if(!empty($request->files)) $_FILES = $request->files;
        if(!empty($request->server)) $_SERVER = $request->server;

    }

    /**
     * @param $uri
     * @return array|null
     * @throws \Exception
     */
    protected function getMvcByUri($uri){
        $mvc = Config::getField('project','mvc');
        $url_array = explode('/', trim($uri,'/'));
        if(!empty($url_array[3])){
            throw new \Exception(402);
        }else{
            if(!empty($url_array[2])){
                $mvc['module'] = $url_array[0];
                $mvc['controller'] = $url_array[1];
                $mvc['action'] = $url_array[2];
            }else if(!empty($url_array[1])){
                $mvc['controller'] = $url_array[0];
                $mvc['action'] = $url_array[1];
            }else if(!empty($url_array[0])){
                $mvc['action'] = $url_array[0];
            }
        }
        $mvc = [
            'module'=>ucwords($mvc['module']),
            'controller'=>ucwords($mvc['controller']),
            'action'=>$mvc['action'],
        ];
        return $mvc;
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
//            Route::initRouteList(Route::getInstance());
            App::init(Factory::getInstance('ZPHP\Core\DI'));
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
