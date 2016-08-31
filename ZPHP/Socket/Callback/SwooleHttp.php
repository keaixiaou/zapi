<?php


namespace ZPHP\Socket\Callback;

use ZPHP\Core,
    ZPHP\Core\Config,
    ZPHP\Core\Swoole,
    ZPHP\Core\Log;
use ZPHP\Protocol;
use ZPHP\Socket\Callback\Swoole as CSwoole;


abstract class SwooleHttp extends CSwoole
{

    protected $currentResponse;
//    protected $responses;//同步一次只接受一个请求,后续需要测试异步是一次性接受几个request
//    protected $currentRequest;

    public function onReceive()
    {
        throw new \Exception('http server must use onRequest');
    }

    public function onWorkerStart($server, $workerId)
    {
        parent::onWorkerStart($server, $workerId);
        Protocol\Request::setHttpServer(1);
        set_error_handler(array($this, 'onErrorHandle'), E_USER_ERROR);
        register_shutdown_function(array($this, 'onErrorShutDown'));
        $common = Config::get('common_file');
        if(!empty($common)){
            require ROOTPATH.$common;
        }
    }


    public function doRequest($request, $response)
    {
        Protocol\Request::setRequest($request);
        Protocol\Response::setResponse($response);
        $this->onRequest($request, $response);
        Protocol\Request::setRequest(null);
        Protocol\Response::setResponse(null);
        $this->afterResponese();
    }


    function onErrorShutDown()
    {
        $error = error_get_last();
        if (!isset($error['type'])) return;
        switch ($error['type'])
        {
            case E_ERROR :
            case E_PARSE :
            case E_USER_ERROR:
            case E_CORE_ERROR :
            case E_COMPILE_ERROR :
                break;
            default:
                return;
        }

        $this->errorResponse($error);
    }


    /**
     * 捕获set_error_handle错误
     */
    public function onErrorHandle($errno, $errstr, $errfile, $errline){
        $error = array(
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
        );
        $this->errorResponse($error);
    }



    public function errorResponse($error){

        $errorMsg = DEBUG===true?"{$error['message']} ({$error['file']}:{$error['line']})":'application internal error!';
        Log::write('error:'.$errorMsg);
        $this->currentResponse->status(500);
        $this->currentResponse->end(Swoole::info($errorMsg));
//        $this->responses[$this->currentRequest->fd]->status(500);
//        $this->responses[$this->currentRequest->fd]->end(Swoole::info($errorMsg));
        $this->afterResponese();
    }

    protected function afterResponese(){
//        unset($this->responses[$this->currentRequest->fd]);
        if (ob_get_contents()) ob_end_clean();
    }

    abstract public function onRequest($request, $response);
}
