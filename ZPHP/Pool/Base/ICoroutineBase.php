<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/9/14
 * Time: 下午2:36
 */

namespace ZPHP\Pool\Base;
interface ICoroutineBase
{
    function send($callback);

    function getResult();
}