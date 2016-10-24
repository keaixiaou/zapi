<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/16
 * Time: 下午2:57
 */


function table($tableName){
    return \ZPHP\Core\Db::getInstance()->table($tableName);
}

function collection($collectionName){
    return \ZPHP\Core\Db::collection($collectionName);
}

