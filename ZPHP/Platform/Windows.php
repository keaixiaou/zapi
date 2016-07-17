<?php
namespace ZPHP\Platform;

class Windows
{
    function kill($pid, $signo)
    {
        return false;
    }

    function fork()
    {
        return false;
    }
}
