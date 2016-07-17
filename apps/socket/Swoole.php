<?php

namespace socket;

use ZPHP\Protocol\Request;
use ZPHP\Socket\Callback\Swoole as ZSwoole;
use ZPHP\Socket\IClient;
use ZPHP\Core\Route as ZRoute;

class Swoole extends ZSwoole
{


    public function onReceive()
    {
        list($serv, $fd, $fromId, $data) = func_get_args();
        if (empty($data)) {
            return;
        }
        Request::parse($data);
        $result = ZRoute::route();
        $serv->send($fd, $result);
    }

}
