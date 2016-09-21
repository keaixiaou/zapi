<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/9/14
 * Time: 下午11:15
 */


namespace ZPHP\Pool\Base;

use ZPHP\Core\Log;
use ZPHP\Pool\MySqlCoroutine;

class CoroutineTask{
    protected $callbackData;
    protected $stack;
    protected $callData;
    protected $routine;
    protected $exception = null;

    public function __construct(\Generator $routine)
    {
        $this->routine = $routine;
        $this->stack = new \SplStack();
    }


    public function work(\Generator $routine){
        while (true) {
            try {
                if (!$routine) {
                    return;
                }
                $value = $routine->current();
//                Log::write(__METHOD__.print_r($value, true));
                //嵌套的协程
                if ($value instanceof \Generator) {
//                    Log::write('嵌套');
                    $this->stack->push($routine);
                    $routine = $value;
                    continue;
                }


                //异步IO的父类
                if(is_subclass_of($value, 'ZPHP\Pool\Base\ICoroutineBase')){
                    $this->stack->push($routine);
                    $value->send([$this, 'callback']);
                    return;
                }

                if ($value instanceof Swoole\Coroutine\RetVal) {

                    // end yeild
                    Log::write(__METHOD__ . " yield end words == " . print_r($value, true), __CLASS__);
                    return false;
                }

                if($value===null) {
                    $return = $routine->getReturn();
                    if(!empty($return)){
                        $this->callbackData = $return;
                    }
//                    Log::write('return:'.__METHOD__.print_r($return, true));
                    if (!$this->stack->isEmpty()) {
                        $routine = $this->stack->pop();
                        $routine->send($this->callbackData);
                        continue;
                    } else {
                        if (!$this->routine->valid()) {
                            return;
                        } else {
                            $this->routine->next();
                            continue;
                        }
                    }
                }else{
                    $this->routine->send($value);
                }

            } catch (\Exception $e) {
                while (!$this->stack->isEmpty()) {
                    $routine = $this->stack->pop();
                    try {
                        $routine->throw($e);
                        break;
                    } catch (\Exception $e) {

                    }
                }
                if ($routine->controller != null) {
                    call_user_func([$routine->controller, 'onExceptionHandle'], $e);
                    $routine->controller = null;
                } else {
                    $routine->throw($e);
                }
            }
        }
    }
    /**
     * [callback description]
     * @param  [type]   $r        [description]
     * @param  [type]   $key      [description]
     * @param  [type]   $calltime [description]
     * @param  [type]   $res      [description]
     * @return function           [description]
     */
    public function callback($data)
    {
        /*
            继续work的函数实现 ，栈结构得到保存
         */
//        Log::write('callback:'.__METHOD__.print_r($data, true));
        $gen = $this->stack->pop();
        $this->callbackData = $data;
        $value = $gen->send($this->callbackData);
        $this->work($gen);

    }


    /**
     * [run 协程调度]
     * @return [type]         [description]
     */
    public function run()
    {
        $routine = &$this->routine;

        try {
            if (!$routine) {
                return;
            }
            $value = $routine->current();
            //嵌套的协程
            if ($value instanceof \Generator) {
                $this->stack->push($routine);
                $routine = $value;
                return;
            }

            if ($value != null) {

                if(method_exists($value, 'getResult')) {
                    $result = $value->getResult();
                }else{
                    $result = $value;
                }
                if ($result !== CoroutineResult::getInstance()) {
                    $routine->send($result);
                }
                //嵌套的协程返回
                while (!$routine->valid() && !$this->stack->isEmpty()) {
                    $result = $routine->getReturn();
                    $this->routine = $this->stack->pop();
                    $this->routine->send($result);
                }
            } else {
                $routine->next();
            }
        } catch (\Exception $e) {
            while (!$this->stack->isEmpty()) {
                $this->routine = $this->stack->pop();
                try {
                    $this->routine->throw($e);
                    break;
                } catch (\Exception $e) {

                }
            }
            if ($routine->controller != null) {
                call_user_func([$routine->controller, 'onExceptionHandle'], $e);
                $routine->controller = null;
            } else {
                $routine->throw($e);
            }
        }
    }

    /**
     * [isFinished 判断该task是否完成]
     * @return boolean [description]
     */
    public function isFinished()
    {
        return $this->stack->isEmpty() && !$this->routine->valid();
    }

    public function getRoutine()
    {
        return $this->routine;
    }
}