<?php

namespace socket;

use library\help;
use ZPHP\Common\Formater;
use ZPHP\Core\Factory;
use ZPHP\Core\Config;
use ZPHP\Core\Log;
use ZPHP\Core\Swoole;
use ZPHP\Protocol\Request;
use ZPHP\Protocol\Response;
use ZPHP\Socket\Callback\SwooleHttp as ZSwooleHttp;
use ZPHP\Socket\IClient;
use ZPHP\Core\Route as ZRoute;

class SwooleHttp extends ZSwooleHttp
{

    public function onRequest($request, $response)
    {
//        $this->currentRequest = $request;
//        $this->responses[$response->fd] = $response;
//        if(count($this->responses)>1){
//            Log::write('responses  in server is more than 1:'.json_encode($this->responses));
//        }

        $this->currentResponse = $response;

        ob_start();
        try {
            $mvc = Config::getField('project','mvc');
            $uri = $request->server['request_uri'];
            if(strpos($uri,'.')!==false){
                throw new \Exception(403);
            }
            $url_array = explode('/', $uri);
            if(!isset($url_array[2])){
                if(!empty($url_array[0])){
                    $mvc['controller'] = $url_array[0];
                };
                if(!empty($url_array[1])){
                    $mvc['action'] = $url_array[1];
                };
            }else{
                if(!empty($url_array[0])){
                    $mvc['module'] = $url_array[0];
                };
                if(!empty($url_array[1])){
                    $mvc['controller'] = $url_array[1];
                };
                if(!empty($url_array[2])){
                    $mvc['action'] = $url_array[2];
                };
            }
            $controller_class = Config::get('ctrl_path', 'controllers') . '\\'
                .ucwords($mvc['module']).'\\'.ucwords($mvc['controller']);
            $controller = Factory::getInstance($controller_class);
            if(!empty(Config::getField('project','reload'))&& extension_loaded('runkit')){
                $controller = Factory::reload($controller_class);
            }
            $action = !empty($controller->is_api)?'apiStart':$mvc['action'];
            if(!method_exists($controller, $action)){
                throw new \Exception(404);
            }
            try{
                call_user_func([$controller,$action]);

            }catch(\Exception $e){
                $response->status(500);
                echo Swoole::info($e->getMessage());
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
        if(!empty($controller->is_api)){
            $response->header('Content-Type', 'application/json');
        }

//        $response->cookie('zphp',md5('zphp'));
        $response->end($result);
    }

    public function onWorkerStart($server, $workerId)
    {
        parent::onWorkerStart($server, $workerId);
    }

}
