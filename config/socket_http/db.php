<?php

    return array(
            'db'=>array(
                'master'=>[
                    'dsn'=>'mysql:host=120.27.143.217;port=3306;dbname=jeekzx',
                    'type' => 'pdo',

                    'user'       => "jeekzx",
                    'password'     => "7f331f",
                    'charset'    => "utf8",
                    'setname'    => true,
//                    'persistent' => true, //MySQL长连接
                    'use_proxy'  => false,  //启动读写分离Proxy
                    ]
            )
    );