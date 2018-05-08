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
    private const APP_CONF_FILE = "config/application/conf.yml";

    /**
     * Developers configuration file path
     */
    private const APP_DEV_FILE = "config/application/developers.yml";

    /**
     * Instance of system
     * @var System|null
     */
    private static $_instance = null;

    /**
     * @var object configurations
     */
    private $_configurations;

    /**
     * Request
     * @var Request
     */
    private $_request;

    /**
     * Response
     * @var Response
     */
    private $_response;


    /**
     * System constructor.
     */
    private function __construct() {

        // Define root path
        $_DIR = str_replace("\\", "/", __DIR__);
        define('ROOT', substr($_DIR, 0, strpos($_DIR, "System")));
        define('SRC', ROOT . 'src/');

        // Activate Autoloader
        require_once "AutoLoader.php";
        // Activate vendor autoloader
        if(file_exists(ROOT . 'vendor/autoload.php')) @require_once ROOT . 'vendor/autoload.php';
        // Load tool
        require_once "tools.php";

        $application_conf = yaml_parse(file_get_contents(ROOT . self::APP_CONF_FILE));
        $developers_conf = array();
        if(file_exists(ROOT . self::APP_DEV_FILE)) {
            $temp = yaml_parse(file_get_contents(ROOT . self::APP_DEV_FILE));
            if(isset($temp['developers_mode']) && $temp['developers_mode']) $developers_conf = $temp;
        }
        $this->_configurations = (object)array_merge($application_conf, $developers_conf);

        session_start();

    }

    /**
     * Start Framework
     */
    public function start(): void {

        try {

            try {

                $this->_request = new Request();

                $this->_response = Router::getInstance()->findResponseURL($this->_request);
                echo $this->_response;

            } catch (HttpNotFoundException $e) {
                if (
                    property_exists($this->_configurations, 'developers_mode')
                    && $this->_configurations->developers_mode
                ) {
                    echo (new ExceptionsWebPage())->indexAction($e);
                } elseif
                (
                    property_exists($this->_configurations, 'http_not_found')
                    && !is_null($routeName = $this->_configurations->http_not_found['route'])
                ) {
                    echo Router::getInstance()->find($routeName)->action();
                }
                else {
                    echo new Response("", Response::HTTP_CODE_NOT_FOUND);
                }
            }

        }
        catch (Exception $e) {
            if(
                property_exists($this->_configurations, 'developers_mode')
                && $this->_configurations->developers_mode
            ) {
                echo (new ExceptionsWebPage())->indexAction($e);
            }
            else {
                echo new Response("", Response::HTTP_CODE_INTERNAL_SERVER_ERROR);
            }
        }
        finally {
            $this->onEnd();
        }

    }

    /**
     * Script of request end
     */
    public function onEnd(): void {
        $this->_request->purge();
    }

    /**
     * Get configuration of app
     * @return object|null
     */
    public function getAppConf(): object {
        return $this->_configurations;
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