<?php

namespace System;

use System\Http\Request;
use System\Http\Response;
use System\Exceptions\ClassNotExtendsControllerException;
use System\Exceptions\HttpNotFoundException;
use System\Exceptions\IncorrectParameterRouteException;
use System\Exceptions\UndefinedRouteClassException;
use System\Exceptions\UndefinedRouteMethodException;

/**
 * Class Router
 * Router system for web page
 * @package System
 * @author Romain Bourré
 */
class Router {

    /**
     * Path file configuration of Router
     */
    private const ROUTING_CONF = 'config/routing/route.yml';

    /**
     * Instance of Router
     * @var Router|null
     */
    private static $_instance = null;

    /**
     * Routing yaml content
     * @var array|null
     */
    private $routing = null;

    /**
     * Router constructor.
     */
    private function __construct() {
        $this->routing = yaml_parse(file_get_contents(ROOT . self::ROUTING_CONF));
    }

    /**
     * Found web page to url
     * @param Request $request web request
     * @param String $url url of web page
     * @return Response
     * @throws ClassNotExtendsControllerException
     * @throws HttpNotFoundException
     * @throws IncorrectParameterRouteException
     * @throws UndefinedRouteClassException
     * @throws UndefinedRouteMethodException
     */
    public function findURL(Request $request, String $url): Response {

        if(!is_null($this->routing)) {

            foreach ($this->routing as $name => $route) {

                if(!is_null(($params = $this->checkURL($route, $url)))) {

                    if(!isset($route['class'])) throw new UndefinedRouteClassException($name);

                    $class = explode("::", $route['class']);
                    return $this->loadWebPage($request, $class[0], $class[1], $params, $name, $url);


                }

            }

        }

        throw new HttpNotFoundException($url);

    }

    /**
     * Load web page from details route
     * @param Request $request
     * @param String $class name of class
     * @param String $method method of class to action
     * @param array $params URL parameters
     * @param String $name name of route
     * @param String $url url of route
     * @return Response Response of web page
     * @throws ClassNotExtendsControllerException
     * @throws UndefinedRouteMethodException
     */
    private function loadWebPage(Request $request, String $class, String $method, array $params, String $name, String $url): Response {

        $webPage = new $class($url);
        if(is_subclass_of($webPage, 'System\Controller')) {
            if (method_exists($webPage, $method)) {
                $params = array_merge(array($request), array_values($params));
                return call_user_func_array(array($webPage, $method), $params);
            } else throw new UndefinedRouteMethodException($name, $method);
        }
        else {
            throw new ClassNotExtendsControllerException(get_class($webPage), $name);
        }

    }

    /**
     * Compare route with browser url
     * @param array $route route
     * @param String $url browser url
     * @return array|null list of parameters
     * @throws IncorrectParameterRouteException
     */
    private function checkURL(Array $route, String $url): ?array {

        $tempArrayRoute = explode('/', $route['url']);
        $tempArrayURL = explode('/', $url);
        $arrayURL = array();
        $arrayRoute = array();
        $paramsURL = array();

        // Clean URL route
        foreach (array_filter($tempArrayRoute, function($a) {
            return !empty($a);
        }) as $key => $value) $arrayRoute[]= $value;

        // Clean URL user
        foreach (array_filter($tempArrayURL, function($a) {
            return !empty($a);
        }) as $key => $value) $arrayURL[] = $value;



        // Check sizes of URLS
        if(sizeof($arrayURL) != sizeof($arrayRoute)) return null;
        if(empty($arrayURL) && empty($arrayRoute)) return array();

        // Compare URLS
        for($i = 0; $i < sizeof($arrayRoute); $i++) {

            $itemRoute = $arrayRoute[$i];
            $itemURL = $arrayURL[$i];

            // Check if parameters is present
            if(preg_match('/^{[a-zA-Z0-9]*}$/', $itemRoute)) {

                $param = substr($itemRoute, 1, strlen($itemRoute)-2);

                // Check param with regex defined in routing file
                if(isset($route['params'][$param])) {
                    $regex = $route['params'][$param];
                    if(!preg_match("/^$regex$/", $itemURL)) throw  new IncorrectParameterRouteException($url, $param);
                }

                // Save parameter
                $paramsURL[$param] = $itemURL;

            }
            else if($itemRoute != $itemURL) {
                return null;
            }

        }

        return $paramsURL;

    }

    /**
     * Get instance of Router
     * @return Router
     */
    public static function getInstance() {
        if(is_null(self::$_instance)) self::$_instance = new self();
        return self::$_instance;
    }





}