<?php

namespace System;

use System\Exceptions\IncorrectFormatConfigurationFileException;
use System\Exceptions\IncorrectRequestMethodException;
use System\Exceptions\RouteNotFoundException;
use System\Http\Request;
use System\Exceptions\HttpNotFoundException;
use System\Exceptions\UndefinedRouteClassException;
use System\Exceptions\UndefinedRouteFuncException;

/**
 * Class Router
 * Router System for web page
 * @package System
 * @author Romain Bourré
 */
class Router {

    /**
     * Path file configuration of Router
     */
    const ROUTING_CONF = 'config/routing/route.yml';

    /**
     * Instance of Router
     * @var Router|null
     */
    private static $_instance = null;

    /**
     * Routing yaml content
     * @var array|null
     */
    private $_conf_routing = null;

    /**
     * Router constructor.
     */
    private function __construct() {
        $this->_conf_routing = yaml_parse(file_get_contents(ROOT . self::ROUTING_CONF));
    }

    /**
     * Found web page to url
     * @param Request $request web request
     * @return Route
     * @throws Exceptions\UndefinedRouteUrlException
     * @throws HttpNotFoundException
     * @throws IncorrectFormatConfigurationFileException
     * @throws UndefinedRouteClassException
     * @throws UndefinedRouteFuncException
     */
    public function findRouteByRequest(Request $request): Route {
        if(!is_null($this->_conf_routing)) {
            $bestRoute = null;
            $tempParam = null;
            $tempException = null;
            $order = array();
            foreach ($this->_conf_routing as $name => $data) {
                if (!is_array($data)) throw new IncorrectFormatConfigurationFileException(self::ROUTING_CONF);
                $route = new Route($name, $data);
                try {
                    if($route->load($request)){
                        $order[0][] = array($route, null);
                    }
                } catch (IncorrectRequestMethodException $e) {
                    $order[1][] = array($route, $e);
                } catch (Exceptions\IncorrectParameterRouteException $e) {
                    $order[2][] = array($route, $e);
                }
            }
            ksort($order);
            foreach ($order as $r) {
                foreach ($r as $rout) {
                    if(is_null($rout[1])) return $rout[0]; else throw $rout[1];
                }
            }

        }
        throw new HttpNotFoundException($request->getUri());
    }

    /**
     * Find route by her name
     * @param String $nameRoute name of route
     * @return Route
     * @throws Exceptions\UndefinedRouteUrlException
     * @throws UndefinedRouteClassException
     * @throws UndefinedRouteFuncException
     * @throws RouteNotFoundException
     * @throws \Exception
     */
    public function find(string $nameRoute): Route {
        if(!is_null($this->_conf_routing)) {
            if(isset($this->_conf_routing[$nameRoute])) return new Route($nameRoute, $this->_conf_routing[$nameRoute]);
        }
        throw new RouteNotFoundException($nameRoute);
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