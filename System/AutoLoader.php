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
        spl_autoload_register(array(__CLASS__, 'sourceAutoloader'));
    }

    /**
     * System class autoloader
     * @param string $className
     */
    private static function systemAutoloader(string $className): void {
        $className = str_replace("\\", "/", $className);
        @include (ROOT . "$className.php");
    }

    /**
     * Source class autoloader
     * @param string $className
     */
    private static function sourceAutoloader(string $className): void {
        $className = str_replace("\\", "/", $className);
        @include (ROOT . "src/$className.php");
    }


}

AutoLoader::register();