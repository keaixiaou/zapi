<?php
/**
 * User: shenzhe
 * Date: 13-6-17
 */


namespace ZPHP\Protocol;
use ZPHP\View\Factory as ZView;

class Response
{
    private static $_response = null;
    static $HTTP_HEADERS = array(
        100 => "100 Continue",
        101 => "101 Switching Protocols",
        200 => "200 OK",
        201 => "201 Created",
        204 => "204 No Content",
        206 => "206 Partial Content",
        300 => "300 Multiple Choices",
        301 => "301 Moved Permanently",
        302 => "302 Found",
        303 => "303 See Other",
        304 => "304 Not Modified",
        307 => "307 Temporary Redirect",
        400 => "400 Bad Request",
        401 => "401 Unauthorized",
        402 => "402 Address error",
        403 => "403 Forbidden",
        404 => "404 Not Found",
        405 => "405 Method Not Allowed",
        406 => "406 Not Acceptable",
        408 => "408 Request Timeout",
        410 => "410 Gone",
        413 => "413 Request Entity Too Large",
        414 => "414 Request URI Too Long",
        415 => "415 Unsupported Media Type",
        416 => "416 Requested Range Not Satisfiable",
        417 => "417 Expectation Failed",
        500 => "500 Internal Server Error",
        501 => "501 Method Not Implemented",
        503 => "503 Service Unavailable",
        506 => "506 Variant Also Negotiates",
    );

    public static function setResponse($response)
    {
        self::$_response = $response;
    }

    public static function getResponse()
    {
        return self::$_response;
    }

    public static function display($model)
    {
        if(null === $model || false === $model) {
            return $model;
        }
        if(is_array($model) && !empty($model['_view_mode'])) {
            $viewMode = $model['_view_mode'];
            unset($model['_view_mode']);
        } else {
            $viewMode = Request::getViewMode();
            if(empty($viewMode)) {
                if (Request::isAjax()) {
                    $viewMode = 'Json';
                } else {
                    $viewMode = 'Php';
                }
            }
        }

        $view = ZView::getInstance($viewMode);
        if ('Php' === $viewMode) {
            $_tpl_file = Request::getTplFile();
            if(is_array($model) && !empty($model['_tpl_file'])) {
                $_tpl_file = $model['_tpl_file'];
                unset($model['_tpl_file']);
            }

            if(empty($_tpl_file)) {
                throw new \Exception("tpl file empty");
            }
            $view->setTpl($_tpl_file);
        }
        $view->setModel($model);
        return $view->display();
    }

    public static function header($key, $val)
    {
        if(self::$_response) {
            self::$_response->header($key, $val);
            return;
        }

        \header("{$key}: {$val}");
    }

    public static function status($code)
    {
        if(self::$_response) {
            self::$_response->status($code);
            return;
        }

        \http_response_code($code);

    }

    public static function setcookie($key,  $value = '', $expire = 0 , $path = '/', $domain  = '', $secure = false , $httponly = false)
    {
        if(self::$_response) {
            self::$_response->cookie($key,  $value, $expire, $path, $domain, $secure, $httponly);
            return;
        }
        \setcookie($key,  $value, $expire, $path, $domain, $secure, $httponly);

    }

    public static function setrawcookie($key,  $value = '', $expire = 0 , $path = '/', $domain  = '', $secure = false , $httponly = false)
    {
        if(self::$_response) {
            self::$_response->rawcookie($key,  $value, $expire, $path, $domain, $secure, $httponly);
        }
        \setrawcookie($key,  $value, $expire, $path, $domain, $secure, $httponly);
    }

}
