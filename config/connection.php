<?php
    return array(
        //tcp 用于管理连接用的
        'connection'=>array(
            'adapter' => 'Redis',
            'pconnect' => true,
            'host' => '192.168.5.252',
            'port' => 6379,
            'timeout' => 5,
            'prefix' => 'zchat'
        )
    );