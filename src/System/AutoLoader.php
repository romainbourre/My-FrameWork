<?php


namespace System;

/**
 * Class AutoLoader
 * @package System
 * @author Romain Bourré (2017)
 */
class AutoLoader {

    /**
     * Registering autoloader
     */
    public static function register() {
        spl_autoload_register(array(__CLASS__, 'systemAutoloader'));
    }

    /**
     * System class autoloader
     * @param String $className
     */
    private static function systemAutoloader(String $className): void {
        $className = str_replace("\\", "/", $className);
        @include (ROOT . "$className.php");
    }

}

AutoLoader::register();