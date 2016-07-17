<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/16
 * Time: 下午2:52
 */


namespace ZPHP\Db;

use ZPHP\Core\Config;
use ZPHP\Model\Model;

class Db {
    public static $instance;
    protected static $db;
    protected static $_tables;
    private static $lastSql;

    function __construct($db_key='master'){

    }

    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new Db;
        }
        return self::$instance;
    }


    public static function table($tableName='', $db_key = 'master'){
        $model = new Model(self::getInstance(),$db_key);
        $model->table = $tableName;
        return $model;
//        if(!isset(self::$_tables[$tableName])){
//            self::$_tables[$tableName] = new Model(self::getInstance(),$db_key);
//            self::$_tables[$tableName]->table = $tableName;
//        }
//        return self::$_tables[$tableName];
    }


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