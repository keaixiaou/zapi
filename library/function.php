<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/16
 * Time: 下午2:57
 */


function table($tableName){
    return \ZPHP\Db\Db::getInstance()->table($tableName);
}


function cache($key, $value='', $expire=3600){
    $cache = \ZPHP\Cache\Factory::getInstance();
    if($value===null){
        return $cache->delete($key);
    }
    if('' === $value){
        return $cache->get($key);
    }
    return $cache->set($key, $value, $expire);


}