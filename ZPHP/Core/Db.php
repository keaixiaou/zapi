<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/16
 * Time: 下午2:52
 */


namespace ZPHP\Core;

use ZPHP\Db\Mongo;
use ZPHP\Model\Model;
use ZPHP\Pool\MysqlAsynPool;

class Db {
    /**
     * @var MysqlAsynPool
     */
    public $mysqlPool;

    public static $instance;
    protected static $db;
    protected static $_tables;
    protected static $_collection;
    private static $lastSql;

    private function __construct(){
        self::$instance = & $this;
    }

    /**
     * @return Db
     */
    public static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new Db();
        }
        return self::$instance;
    }


    /**
     * @param $workId
     * 初始化mysql连接池
     */
    static public function initMysqlPool($workId){
        if(empty(self::$instance->mysqlPool)) {
            self::$instance->mysqlPool = new MysqlAsynPool();
            self::$instance->mysqlPool->initWorker($workId);
        }
    }


    /**
     * @param string $tableName
     * @param string $db_key
     * @return Model
     */
    public static function table($tableName='', $db_key = 'master'){
        if(!isset(self::$_tables[$tableName])){
            self::$_tables[$tableName] = new Model(self::$instance->mysqlPool);
            self::$_tables[$tableName]->table = $tableName;
        }
        return self::$_tables[$tableName];
    }


    public static function collection($collectName = ''){
        if(!isset(self::$_collection[$collectName])){
            $config = Config::get('mongo');
            $host = 'mongodb://'.(!empty($config['username'])?"{$config['username']}":'')
                .(!empty($config['password'])?":{$config['password']}@":'')
                .$config['host'].(!empty($config['port'])?":{$config['port']}":'');
            $config['dsn'] = $host;
            $mongo = new Mongo();
            $mongo->connect($config);
            $mongo->setDBName($config['database']);
            $mongo->selectCollection($collectName);
            self::$_collection[$collectName] = $mongo;
            unset($mongo);
        }
        return self::$_collection[$collectName];
    }

    /**
     * pdo 查询获取pdo
     * @param string $db_key
     * @return mixed
     * @throws \Exception
     */
    public function getDb($db_key= 'master'){
        if(!isset(self::$db[$db_key])){
            $config = Config::getField('db', $db_key);
            if($config['type']=='pdo'){
                if(empty($config['persistent'])) {
                    self::$db[$db_key] = new \PDO($config['dsn'], $config['user'], $config['password']);
                }else{
                    self::$db[$db_key] = new \PDO($config['dsn'], $config['user'], $config['password'],
                        array(\PDO::ATTR_PERSISTENT => true));
                }
                if(!empty($config['charset'])){
                    self::$db[$db_key]->query('set names ' . $config['charset']);
                }
                self::$db[$db_key]->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

            }
        }
        return self::$db[$db_key];
    }

    public static function setSql($sql){
        self::$lastSql = $sql;
    }

    public static function getLastSql(){
        return self::$lastSql;
    }




}