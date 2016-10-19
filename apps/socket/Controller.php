<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/9/14
 * Time: 下午5:16
 */

namespace socket;
use ZPHP\Core\Log;
use ZPHP\Core\Swoole;

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
        $this->response->end($result);
        $this->destroy();
    }

    /**
     * 异常处理
     */
    public function onExceptionHandle(\Exception $e){
        $msg = DEBUG===true?$e->getMessage():'服务器升空了!';
        $this->response->end(json_encode(['code'=>500,'msg'=>$msg]));
        $this->destroy();
    }

    /**
     * 系统异常错误处理
     * @param $message
     */
    public function onSystemException($message){
        $this->response->end(json_encode(['code'=>500,'msg'=>'系统出现了异常:'.$message]));
        $this->destroy();
    }

    protected function destroy(){
        unset($this->response);
    }
}