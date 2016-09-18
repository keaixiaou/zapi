<?php

    return [
            'db'=>[
//                'master'=>[
//                    'dsn'=>'mysql:host=127.0.0.1;port=3306;dbname=jeekzx',
//                    'type' => 'pdo',
//
//                    'user'       => "jeekzx",
//                    'password'     => "7f331f",
//                    'charset'    => "utf8",
//                    'setname'    => true,
////                    'persistent' => true, //MySQL长连接
//                    'use_proxy'  => false,  //启动读写分离Proxy
//                    ]
                    'master' => [
                        'host' => '127.0.0.1',
                        'user' => 'jeekzx',
                        'password' => '7f331f',
                        'database' => 'jeekzx',
                        'asyn_max_count' => 4,
                    ],
                ]

    ];