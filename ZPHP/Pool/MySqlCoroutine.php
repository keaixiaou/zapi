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

    public function __construct($mysqlAsynPool, $_bind_id = null, $_sql = null)
    {
        $this->result = CoroutineResult::getInstance();
        $this->_mysqlAsynPool = $mysqlAsynPool;
        $this->bind_id = $_bind_id;
        $this->sql = $_sql;
        $this->send(function ($result) {
            $this->result = $result;
        });
    }

    /**
     * @param $callback
     * @throws \Exception
     */
    public function send($callback)
    {
        $this->_mysqlAsynPool->query($callback, $this->bind_id, $this->sql);
    }

    public function getResult()
    {
        return $this->result;
    }
}