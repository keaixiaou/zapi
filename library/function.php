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


function getAbcd(){
    return 'abc1d';
}