<?php

namespace System;

use System\FrameworkWebPage\ExceptionsWebPage\ExceptionsWebPage;
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
     * Configuration path
     */
    private const CONF_PATH = "config/";

    /**
     * Instance of system
     * @var System|null
     */
    private static $_instance = null;

    /**
     * Configuration of application
     * @var null
     */
    private $_application_conf = null;

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

        $this->_application_conf = yaml_parse(file_get_contents(ROOT . self::CONF_PATH . 'application/conf.yml'));

        try {

            $this->_request = new Request();

            $this->_response = Router::getInstance()->findURL($this->_request, $_SERVER['REQUEST_URI']);
            echo $this->_response;

        }
        catch(HttpNotFoundException $e) {
            if(isset($this->_application_conf['development']['mode']) && $this->_application_conf['development']['mode']) echo (new ExceptionsWebPage())->indexAction($e);
        }
        catch (\Exception $e) {
            if(isset($this->_application_conf['development']['mode']) && $this->_application_conf['development']['mode']) echo (new ExceptionsWebPage())->indexAction($e);
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