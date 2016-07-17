<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/15
 * Time: 下午4:56
 */

namespace ZPHP\Core;


class Filter{
    public static function escape($string){
        if (is_numeric($string))
        {
            return $string;
        }
        $string = htmlspecialchars($string, ENT_QUOTES, 'utf-8');
        return $string;
    }
}