<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/9/14
 * Time: 下午2:34
 */

namespace ZPHP\Coroutine\Base;


class CoroutineResult
{
    private static $instance;

    public function __construct()
    {
        self::$instance = &$this;
    }

    public static function &getInstance()
    {
        if (self::$instance == null) {
            new CoroutineResult();
        }
        return self::$instance;
    }
}