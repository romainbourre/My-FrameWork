<?php

namespace System;

use System\Exceptions\IncorrectFormatConfigurationFileException;
use System\Exceptions\IncorrectRequestMethodException;
use System\Exceptions\RouteNotFoundException;
use System\Http\Request;
use System\Http\Response;
use System\Exceptions\HttpNotFoundException;
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
     * @return Response
     * @throws HttpNotFoundException
     * @throws IncorrectFormatConfigurationFileException
     * @throws IncorrectRequestMethodException
     * @throws \Exception
     */
    public function findResponseURL(Request $request): Response {
        if(!is_null($this->_conf_routing)) {
            $bestRoute = null;
            $tempException = null;
            foreach ($this->_conf_routing as $name => $data) {
                if(!is_array($data)) throw new IncorrectFormatConfigurationFileException(self::ROUTING_CONF);
                try {
                    $route = new Route($name, $data);
                    if (!is_null($params = $route->checkURL($request->getUri()))) {
                        if(!is_null($expectMethod = $route->getHttpMethod()) && $expectMethod != ($findMethod = $request->getMethod())) throw new IncorrectRequestMethodException($findMethod, $expectMethod);
                        $bestRoute = $route->action(array_merge(array($request), array_values($params)));
                        $tempException = null;
                    }
                }
                catch (IncorrectRequestMethodException $e) {
                    throw $e;
                }
                catch (\Exception $e) {
                    $tempException = $e;
                }
            }
            if(!is_null($bestRoute) && is_null($tempException)) return $bestRoute; else if (!is_null($tempException)) throw $tempException;
        }
        throw new HttpNotFoundException($request->getUri());
    }

    /**
     * Find route by her name
     * @param String $nameRoute name of route
     * @return Route
     * @throws Exceptions\UndefinedRouteUrlException
     * @throws UndefinedRouteClassException
     * @throws UndefinedRouteMethodException
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