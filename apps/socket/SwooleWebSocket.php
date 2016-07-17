<?php

namespace socket;

use ZPHP\Protocol\Request;
use ZPHP\Socket\Callback\SwooleWebSocket as ZSwooleWebSocket;
use ZPHP\Socket\IClient;
use ZPHP\Core\Route as ZRoute;

class SwooleWebSocket extends ZSwooleWebSocket
{

    private $buff = [];

    public function onMessage($server, $frame)
    {
        if(empty($frame->finish)) { //数据未完
            if(empty($this->buff[$frame->fd])) {
                $this->buff[$frame->fd] = $frame->data;
            } else {
                $this->buff[$frame->fd].=$frame->data;
            }
        } else {
            if(!empty($this->buff[$frame->fd])) {
                $frame->data = $this->buff[$frame->fd].$frame->data;
                unset($this->buff[$frame->fd]);
            }
        }
        Request::parse($frame->data);
        $result = ZRoute::route();
        $server->push($frame->fd, $result);
    }

}
