<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/9/14
 * Time: 下午2:58
 */

namespace ZPHP\Pool;

use ZPHP\Core\Config;
use ZPHP\Core\Log;
use ZPHP\Pool\Base\AsynPool;

class MysqlAsynPool extends AsynPool{

    const AsynName = 'mysql';

    /**
     * @var array
     */
    public $bind_pool;
    protected $mysql_max_count = 0;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 作为客户端的初始化
     * @param $worker_id
     */
    public function initWorker($workId)
    {
        parent::initWorker($workId);
        $this->config = Config::getField('database','master');
    }

    /**
     * 执行一个sql语句
     * @param $callback
     * @param $bind_id 绑定的连接id，用于事务
     * @param $sql
     */
    public function query(callable $callback,  $sql = null)
    {
        $data = [
            'sql' => $sql
        ];
        $data['token'] = $this->addTokenCallback($callback);
        call_user_func([$this, 'execute'], $data);
    }

    /**
     * 执行mysql命令
     * @param $data
     */
    public function execute($data)
    {
        if ($this->pool->isEmpty()) {//代表目前没有可用的连接
            $this->prepareOne($data);
            $this->commands->enqueue($data);
            return;
        } else {
            $client = $this->pool->dequeue();
        }

        $sql = $data['sql'];
        $client->query($sql, function ($client, $result) use ($data) {
            try {
                if ($result === false) {
                    if ($client->errno == 2006 || $client->errno == 2013) {//断线重连
                        $this->reconnect($data, $client);
                        unset($client);
                        $this->commands->unshift($data);
                    } else {
                        throw new \Exception("[mysql客户端操作失败]:" . $client->error . "[sql]:" . $data['sql']);
                    }
                } else {
                    $data['result']['client_id'] = $client->client_id;
                    $data['result']['result'] = $result;
                    $data['result']['affected_rows'] = $client->affected_rows;
                    $data['result']['insert_id'] = $client->insert_id;
                    unset($data['sql']);
                    //不是绑定的连接就回归连接
                    $this->pushToPool($client);

                    //给worker发消息
                    call_user_func([$this, 'distribute'], $data);

                }
            }catch(\Exception $e){
                $data['result']['exception'] = $e->getMessage();
                call_user_func([$this, 'distribute'], $data);
            }
        });
    }

    /**
     * 重连或者连接
     * @param array $data['token'] 异常回调的索引
     * @param null $client
     */
    public function reconnect($data, $tmpClient = null)
    {
        if ($tmpClient == null) {
            $client = new \swoole_mysql();
        }else{
            $client = $tmpClient;
        }
        $set = $this->config;
        $nowConnectNo = $this->mysql_max_count;
        unset($set['asyn_max_count']);
        $client->connect($set, function ($client, $result) use($tmpClient,$nowConnectNo, $data) {
            try {
                if (!$result) {
                    // $this->mysql_max_count --;
                    throw new \Exception("[mysql连接失败]".$client->connect_error);
                } else {
                    $client->isAffair = false;
                    $client->client_id = $tmpClient ? $tmpClient->client_id : $nowConnectNo;
//                Log::write(__METHOD__.print_r($client, true));
                    $this->pushToPool($client);
                }
            }catch(\Exception $e){
                $data['result']['exception'] = $e->getMessage();
                call_user_func([$this, 'distribute'], $data);
            }
        });
    }

    /**
     * 准备一个mysql
     */
    public function prepareOne($data)
    {
        if ($this->mysql_max_count >= $this->config['asyn_max_count']) {
            return;
        }

        $this->mysql_max_count ++;
        $this->reconnect($data);
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