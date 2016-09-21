<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/9/14
 * Time: 下午5:16
 */

namespace socket;
use ZPHP\Core\Log;

class Controller {
    /**
     * @var $response
     */
    public $response;
    public $method;



    /**
     * api接口请求总入口
     *
    */
    public function coroutineApiStart(){
        $result = yield call_user_func([$this, $this->method]);
        $result = json_encode($result);
        Log::write('result:'.($result),Log::INFO);
        $this->response->end($result);
        $this->destroy();
    }

    /**
     * 异常处理
     */
    public function onExceptionHandle(\Exception $e){
        $msg = DEBUG===true?$e->getMessage():'服务器升空了!';
        $this->response->end(Swoole::info($msg));
        $this->destroy();
    }


    protected function destroy(){
        unset($this->response);
    }
}