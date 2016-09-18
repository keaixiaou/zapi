<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/9/14
 * Time: 下午2:58
 */

namespace ZPHP\Pool;

use ZPHP\Core\Log;
use ZPHP\Model\Model;
use ZPHP\Pool\Base\AsynPool;

class MysqlAsynPool extends AsynPool{

    const AsynName = 'mysql';

    /**
     * @var Model
     */
    public $dbQueryBuilder;
    /**
     * @var array
     */
    public $bind_pool;
    protected $mysql_max_count = 0;

    public function __construct()
    {
        parent::__construct();
        $this->bind_pool = [];
    }

    /**
     * 作为客户端的初始化
     * @param $worker_id
     */
    public function initWorker($workId)
    {
        parent::initWorker($workId);
        $this->dbQueryBuilder = new Model($this);
    }

    /**
     * 执行一个sql语句
     * @param $callback
     * @param $bind_id 绑定的连接id，用于事务
     * @param $sql
     */
    public function query($callback, $bind_id = null, $sql = null)
    {
//        if ($sql == null) {
//            $sql = $this->dbQueryBuilder->makeSql(false);
//        }

        if (empty($sql)) {
            throw new \Exception('sql empty');
        }
        $data = [
            'sql' => $sql
        ];
        $data['token'] = $this->addTokenCallback($callback);
        call_user_func([$this, 'execute'], $data);
        //写入管道
//        $this->asyn_manager->writePipe($this, $data, $this->worker_id);
    }

    /**
     * 执行mysql命令
     * @param $data
     */
    public function execute($data)
    {
        $client = null;
//        $bind_id = $data['bind_id']?null:$data['bind_id'];
//        if ($bind_id != null) {//绑定
//            $client = $this->bind_pool[$bind_id]['client'];
//        }
        if ($client == null) {
            if (count($this->pool) == 0) {//代表目前没有可用的连接
                $this->prepareOne();
                $this->commands->push($data);
                return;
            } else {
                $client = $this->pool->shift();
            }
        }
//        else {
//            if ($client->isAffair) {//如果已经在处理事务了就放进去
//                $this->bind_pool[$bind_id]['affairs'][] = $data;
//                return;
//            }
//        }

        $sql = $data['sql'];
        $client->query($sql, function ($client, $result) use ($data) {
            if ($result === false) {
                if ($client->errno == 2006 || $client->errno == 2013) {//断线重连
                    $this->reconnect($client);
                    $this->commands->unshift($data);
                } else {
                    throw new \Exception("[mysql]:" . $client->error . "[sql]:" . $data['sql']);
                }
            }
            $data['result']['client_id'] = $client->client_id;
            $data['result']['result'] = $result;
            $data['result']['affected_rows'] = $client->affected_rows;
            $data['result']['insert_id'] = $client->insert_id;
            unset($data['sql']);
            //给worker发消息
            call_user_func([$this, 'distribute'], $data);
            //不是绑定的连接就回归连接
            $this->pushToPool($client);
        });
    }

    /**
     * 重连或者连接
     * @param null $client
     */
    public function reconnect($tmpClient = null)
    {
        if ($tmpClient == null) {
            $client = new \swoole_mysql();
        }else{
            $client = $tmpClient;
        }
        $set = $this->config;
        unset($set['asyn_max_count']);
        $client->connect($set, function ($client, $result) use($tmpClient) {
            if (!$result) {
               // $this->mysql_max_count --;
                throw new \Exception($client->connect_error);
            } else {
                $client->isAffair = false;
                $client->client_id = $this->mysql_max_count;
                $this->pushToPool($client);
            }
        });
    }

    /**
     * 准备一个mysql
     */
    public function prepareOne()
    {
        if($this->prepareLock) return;
        if ($this->mysql_max_count >= $this->config['asyn_max_count']) {
            return;
        }

        $this->mysql_max_count ++;
        $this->prepareLock = true;
        $this->reconnect();
    }

    /**
     * @return string
     */
    public function getAsynName()
    {
        return self::AsynName;
    }

    /**
     * @return int
     */
    public function getMessageType()
    {
        return SwooleMarco::MSG_TYPE_MYSQL_MESSAGE;
    }




}