<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/9/22
 * Time: 下午5:02
 */


namespace ZPHP\Redis;

use ZPHP\Coroutine\Redis\RedisCoroutine;

class Redis{
    protected $pool;

    function __construct($redisPool){
        $this->pool = $redisPool;
    }

    //redis操作
    public function cache($key, $value='', $expire=3600){
        $redisCoroutine = new RedisCoroutine($this->pool);
        yield $redisCoroutine->cache($key, $value, $expire);
    }

}