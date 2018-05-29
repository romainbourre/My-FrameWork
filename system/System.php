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
     * App variables file path
     */
    private const APP_VAR_FILE = "config/application/variables.yml";

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
     * @var array variables
     */
    private $_variables = array();

    /**
     * Request
     * @var Request
     */
    private $_request;

    /**
     * @var Route
     */
    private $_route;


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

        if(file_exists(ROOT . self::APP_VAR_FILE)) {
            $this->_variables = yaml_parse(file_get_contents(ROOT . self::APP_VAR_FILE));
        }

        session_start();

    }

    /**
     * Start Framework
     */
    public function start(): void {
        try {
            $this->_request = new Request();
            $this->_route = Router::getInstance()->findRouteByRequest($this->_request);
            $this->_route->exec();
        }
        catch(HttpNotFoundException $e) {
            try {
                Router::getInstance()->find("httpNotFound")->exec();
            }
            catch(Exception $e) {
                echo $this->runException($e, new Response("", Response::HTTP_CODE_NOT_FOUND));
            }
        }
        catch (Exception $e) {
            echo $this->runException($e, new Response("", Response::HTTP_CODE_INTERNAL_SERVER_ERROR));
        }
        finally {
            $this->onEnd();
        }
    }

    /**
     * Script of request end
     */
    public function onEnd(): void {
        if(!is_null($this->_request)) $this->_request->purge();
    }

    /**
     * Get configuration of app
     * @return object|null
     */
    public function getAppConf(): object {
        return $this->_configurations;
    }

    /**
     * Get variables of application
     * @return array
     */
    public function getVars(): array {
        return $this->_variables;
    }

    /**
     * Get current request
     * @return Request
     */
    public function getRequest(): Request {
        return $this->_request;
    }

    /**
     * Load instance of System
     * @return System
     */
    public static function get(): System {
        if(is_null(self::$_instance)) self::$_instance = new self();
        return self::$_instance;
    }

    /**
     * Return exception web page if developers mode activate,
     * response given else
     * @param Exception $e exception
     * @param Response $r response else
     * @return Response response
     */
    private function runException(Exception $e, Response $r): Response {
        if(property_exists($this->_configurations, 'developers_mode') && $this->_configurations->developers_mode ) return (new ExceptionsWebPage())->indexAction($e);
        return $r;
    }

}

return System::get();