<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/9/14
 * Time: 下午11:15
 */


namespace ZPHP\Coroutine\Base;

use ZPHP\Core\Log;

class CoroutineTask{
    protected $callbackData;
    protected $stack;
    protected $callData;
    protected $routine;
    protected $exception = null;
    protected $i;

    public function __construct(\Generator $routine)
    {
        $this->routine = $routine;
        $this->stack = new \SplStack();
        $this->i = 1;
    }


    /**
     * 协程调度器
     * @param \Generator $routine
     */
    public function work(\Generator $routine){
        while (true) {
//            Log::write("this'i : ".$this->i);
            $this->i++;
            try {
                if(!empty($this->exception)){
                    throw new \Exception($this->exception);
                }
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
                if(is_subclass_of($value, 'ZPHP\Coroutine\Base\ICoroutineBase')){
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
//                    Log::write(__METHOD__.';last value:'.print_r($value, true));
                    $this->routine->send($value);
                }

            } catch (\Exception $e) {
//                Log::write('exception:' . print_r($e, true));
                while(!$this->stack->isEmpty()) {
                    $routine = $this->stack->pop();
//                    Log::write('routine:' . print_r($routine, true));
                }
                call_user_func_array([$this->routine->controller, 'onExceptionHandle'], ['e'=>$e]);
                break;
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
        if(!empty($data['exception'])){
            call_user_func_array([$this->routine->controller, 'onSystemException'],
                ['message'=>$data['exception']]);
        }else {
            $gen = $this->stack->pop();
            $this->callbackData = $data;
            $value = $gen->send($this->callbackData);
            $this->work($gen);
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