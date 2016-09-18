<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/9/14
 * Time: 下午2:55
 */


namespace ZPHP\Pool\Base;


use ZPHP\Core\Config;
use ZPHP\Core\Log;

abstract class AsynPool implements IAsynPool
{
    const MAX_TOKEN = 655360;
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
        if ($callback != null) {
            call_user_func($callback, $data['result']);
        }
    }

    /**
     * @param $swoole_server
     * @param $asyn_manager
     */
    public function initServer($swoole_server, $asyn_manager)
    {
//        $this->config = $swoole_server->config;
//        $this->swoole_server = $swoole_server;
//        $this->server = $swoole_server->server;
//        $this->asyn_manager = $asyn_manager;
    }

    /**
     * @param $workerid
     */
    public function initWorker($workerId)
    {
        $this->config = Config::getField('db','master');
        $this->workerId = $workerId;
    }

    /**
     * @param $client
     */
    public function pushToPool($client)
    {
        $this->prepareLock = false;
        $this->pool->push($client);
        if (count($this->commands) > 0) {//有残留的任务
            $command = $this->commands->shift();
            $this->execute($command);
        }
    }
}