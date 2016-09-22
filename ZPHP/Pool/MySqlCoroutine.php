<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/9/14
 * Time: 下午3:47
 */


namespace ZPHP\Pool;

use ZPHP\Core\Log;
use ZPHP\Pool\Base\CoroutineResult;
use ZPHP\Pool\Base\ICoroutineBase;

class MySqlCoroutine implements ICoroutineBase{
    /**
     * @var MysqlAsynPool
     */
    public $_mysqlAsynPool;
    public $bind_id;
    public $sql;
    public $result;

    public function __construct($mysqlAsynPool)
    {
        $this->result = CoroutineResult::getInstance();
        $this->_mysqlAsynPool = $mysqlAsynPool;
//        $this->send(function ($result) {
//            $this->result = $result;
//        });
    }

    public function query($sql){
        $this->sql = $sql;
        yield $this;
    }


    /**
     * @param $callback
     * @throws \Exception
     */
    public function send(callable $callback)
    {
        $this->_mysqlAsynPool->query($callback, $this->sql);
    }

    public function getResult()
    {
        return $this->result;
    }
}