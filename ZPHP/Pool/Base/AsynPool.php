<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/9/14
 * Time: 下午2:55
 */


namespace ZPHP\Pool\Base;


abstract class AsynPool implements IAsynPool
{
    const MAX_TOKEN = DEBUG===true?10:650000;
    protected $commands;
    protected $pool;
    protected $callBacks;
    protected $workerId;
    protected $server;
    protected $swoole_server;
    protected $token = 0;
    //避免爆发连接的锁
    protected $prepareLock = false;
    /**
     * @var AsynPoolManager
     */
    protected $asyn_manager;
    /**
     * @var Config
     */
    protected $config;

    public function __construct()
    {
        $this->callBacks = new \SplFixedArray(self::MAX_TOKEN);
        $this->commands = new \SplQueue();
        $this->pool = new \SplQueue();
    }

    public function addTokenCallback($callback)
    {
        $token = $this->token;
        $this->callBacks[$token] = $callback;
        $this->token++;
        if ($this->token >= self::MAX_TOKEN) {
            $this->token = 0;
        }
        return $token;
    }

    /**
     * 分发消息
     * @param $data
     */
    public function distribute($data)
    {
        $callback = $this->callBacks[$data['token']];
        unset($this->callBacks[$data['token']]);
//        Log::write(__METHOD__.print_r($this->pool));
        if ($callback != null) {
            call_user_func_array($callback, ['data'=>$data['result']]);
//            call_user_func($callback, $data['result']);
        }
    }


    /**
     * @param $workerid
     */
    public function initWorker($workerId)
    {
        $this->workerId = $workerId;
    }

    /**
     * @param $client
     */
    public function pushToPool($client)
    {
        $this->prepareLock = false;
        $this->pool->push($client);
        if (!$this->commands->isEmpty()) {//有残留的任务
            $command = $this->commands->dequeue();
            $this->execute($command);
        }
    }

    /**
     *
     */
    public function freeCallback(){
        unset($this->callBacks);
    }
}