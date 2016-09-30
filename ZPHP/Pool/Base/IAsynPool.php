<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/9/14
 * Time: 下午2:46
 */

namespace ZPHP\Pool\Base;

interface IAsynPool
{
    function getAsynName();

    function distribute($data);

    function execute($data);

    function initWorker($workerId);


    function getMessageType();



    function pushToPool($client);

    function prepareOne($data);

    function addTokenCallback($callback);
}