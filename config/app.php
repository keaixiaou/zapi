<?php

use \ZPHP\Socket\Adapter\Swoole;

return array(
    'server_mode' => 'Socket',
    'project_name' => 'zapi',
    'app_path' => 'apps',
    'ctrl_path' => 'controller',
    'common_file'  => '/library/function.php',
    'response_filter' => true,
    'socket' => array(
        'host' => '0.0.0.0',                          //socket 监听ip
        'port' => 8991,                             //socket 监听端口
        'adapter' => 'Swoole',                          //socket 驱动模块
        'server_type' => Swoole::TYPE_HTTP,              //socket 业务模型 tcp/udp/http/websocket
        'protocol' => 'Http',                         //socket通信数据协议
        'daemonize' => 1,                             //是否开启守护进程
        'client_class' => 'socket\\SwooleHttp',            //socket 回调类
        'work_mode' => 3,                             //工作模式：1：单进程单线程 2：多线程 3： 多进程
        'worker_num' => 1,                                 //工作进程数
        'max_request' => 0,                            //单个进程最大处理请求数
        'debug_mode' => 1,
        'log_file' => ROOTPATH.'/log/swoole.log',//打开调试模式
    ),

    'project'=>array(
        'type' => 'api',
        'view_mode'=>'Stringv',   		//view模式
        'pid_path'  => ROOTPATH.'/webroot',
        'mvc'  => [
            'module'=>'Home',
            'controller' => 'Index',
            'action' => 'index'
            ],
        'reload' => DEBUG,
    )

);
