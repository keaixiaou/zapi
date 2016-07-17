<?php
/**
 * author: shenzhe
 * Date: 13-6-17
 */
namespace ZPHP\Core;

class Factory
{
    private static $instances = array();

    public static function getInstance($className, $params = null)
    {
        $keyName = $className;
        if (!empty($params['_prefix'])) {
            $keyName .= $params['_prefix'];
        }
        if (isset(self::$instances[$keyName])) {
            return self::$instances[$keyName];
        }
        if (!\class_exists($className)) {
            return null;
        }
        if (empty($params)) {
            self::$instances[$keyName] = new $className();
        } else {
            self::$instances[$keyName] = new $className($params);
        }
        return self::$instances[$keyName];
    }

    //用来重载controller文件
    public static function reload($className, $params=null){
        $keyName = $className;
        if (!empty($params['_prefix'])) {
            $keyName .= $params['_prefix'];
        }

        $controller_file = ROOTPATH.'/apps/'.str_replace('\\','/',$className).'.php';
        if(!is_file($controller_file)){
            throw new \Exception("no file {$controller_file}");
        }
        runkit_import($controller_file, RUNKIT_IMPORT_CLASS_METHODS|RUNKIT_IMPORT_OVERRIDE);
        if (!\class_exists($className)) {
            throw new \Exception("no class {$className}");
        }
        $class = $params? new $className($params): new $className();
        self::$instances[$keyName] = $class;
        return self::$instances[$keyName];
    }
}
