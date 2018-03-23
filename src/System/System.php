<?php

namespace System;

use System\Http\Request;
use System\Http\Response;
use System\Exceptions\HttpNotFoundException;

/**
 * Framework System
 * @package System
 * @author Romain Bourré
 */
class System {

    /**
     * Instance of system
     * @var System|null
     */
    private static $_instance = null;

    /**
     * Request
     * @var null|Request
     */
    private $_request = null;

    /**
     * Response
     * @var null|Response
     */
    private $_response = null;

    /**
     * System constructor.
     */
    private function __construct() {

        session_start();

        // Define root path
        $_DIR = str_replace("\\", "/", __DIR__);
        define('ROOT', substr($_DIR, 0, strpos($_DIR, "System")));

        // Activate Autoloader
        require_once "AutoLoader.php";

        try {

            $this->_request = new Request();

            $this->_response = Router::getInstance()->findURL($this->_request, $_SERVER['REQUEST_URI']);
            echo $this->_response;

        }
        catch(HttpNotFoundException $e) {
            var_dump($e);
        }
        catch (\Exception $e) {
            var_dump($e);
        }

    }

    /**
     * Load instance of System
     * @return System
     */
    public static function start() {
        if(is_null(self::$_instance)) self::$_instance = new self();
        return self::$_instance;
    }

}

System::start();