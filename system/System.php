<?php

namespace System;

use System\FrameworkWebPage\ExceptionsWebPage\ExceptionsWebPage;
use System\Http\Request;
use System\Http\Response;
use System\Exceptions\HttpNotFoundException;
use \Exception;

/**
 * Framework System
 * @package System
 * @author Romain Bourré
 */
class System {

    /**
     * App configuration file path
     */
    public const APP_CONF_FILE = "config/application/conf.yml";

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
        define('SRC', ROOT . 'src/');

        // Activate Autoloader
        require_once "AutoLoader.php";
        // Activate vendor autoloader
        @require_once ROOT . 'vendor/autoloader.php';
        // Load tool
        require_once "tools.php";

        $this->_application_conf = yaml_parse(file_get_contents(ROOT . self::APP_CONF_FILE));

    }

    /**
     * Start Framework
     */
    public function start(): void {

        try {

            $this->_request = new Request();

            $this->_response = Router::getInstance()->findResponseURL($this->_request);
            echo $this->_response;

        }
        catch(HttpNotFoundException $e) {
            if(isset($this->_application_conf['development']['mode']) && $this->_application_conf['development']['mode']) echo (new ExceptionsWebPage())->indexAction($e);
        }
        catch (Exception $e) {
            if(isset($this->_application_conf['development']['mode']) && $this->_application_conf['development']['mode']) echo (new ExceptionsWebPage())->indexAction($e);
        }

    }

    /**
     * Get configuration of app
     * @return array|null
     */
    public function getAppConf(): ?array {
        return $this->_application_conf;
    }

    /**
     * Load instance of System
     * @return System
     */
    public static function get(): System {
        if(is_null(self::$_instance)) self::$_instance = new self();
        return self::$_instance;
    }

}

return System::get();