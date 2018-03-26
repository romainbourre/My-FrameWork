<?php

namespace System;

use System\Exceptions\ClassNotExtendsControllerException;
use System\Exceptions\IncorrectFormatConfigurationFileException;
use System\Exceptions\IncorrectParameterRouteException;
use System\Exceptions\TooFewParametersException;
use System\Exceptions\TooManyParametersException;
use System\Exceptions\UndefinedRouteClassException;
use System\Exceptions\UndefinedRouteMethodException;
use System\Exceptions\UndefinedRouteUrlException;
use System\Http\Response;
use System\Interfaces\WebPage;

/**
 * Class Route
 * Represent route of routing system
 * @package System
 * @author Romain Bourré
 */
class Route {

    /**
     * @var null|String name of route
     */
    private $name = null;

    /**
     * @var mixed|null url of route
     */
    private $url = null;

    /**
     * @var null class of route
     */
    private $class = null;

    /**
     * @var null $method of class
     */
    private $method = null;

    /**
     * @var mixed|null regex of parameters
     */
    private $regex = null;

    /**
     * @var null parameters of url
     */
    private $parameters = null;

    /**
     * @var null|String Class asked to authentication of routing
     */
    private $auth_class = null;

    /**
     * @var null|String Method asked to authentication of routing
     */
    private $auth_method = null;

    /**
     * @var mixed|null Expected value to authentication
     */
    private $auth_expected = null;

    /**
     * @var mixed|null Redirection route when authentication reject
     */
    private $auth_redirect = null;

    /**
     * @var null Web page of route
     */
    private $webpage = null;

    /**
     * Route constructor.
     * @param String $name
     * @param array $data
     * @throws UndefinedRouteClassException
     * @throws UndefinedRouteMethodException
     * @throws UndefinedRouteUrlException
     * @throws \Exception
     */
    public function __construct(String $name, array $data) {
        $this->name = $name;
        if(isset($data['url'])) $this->url = $data['url']; else throw new UndefinedRouteUrlException($name);
        if(isset($data['class'])) $action = $data['class']; else throw new UndefinedRouteClassException($name);
        $action = explode("::", $action);
        if(isset($action[0]) && class_exists($action[0])) $this->class = $action[0]; else throw new UndefinedRouteClassException($name);
        if(isset($action[1]) && method_exists($this->class, $action[1])) $this->method = $action[1]; else throw new UndefinedRouteMethodException($name, $action[1]);
        if(isset($data['params'])) $this->regex = $data['params'];
        $this->findParameters();
        if(isset($data['auth'])) {
            if(!is_array($data['auth']) || !isset($data['auth']['class']) || empty($data['auth']['class'])) throw new IncorrectFormatConfigurationFileException(Router::ROUTING_CONF);
            $arrayClass = explode('::', $data['auth']['class']);
            if(sizeof($arrayClass) != 2) throw new IncorrectFormatConfigurationFileException(Router::ROUTING_CONF);
            if(class_exists($arrayClass[0])) {
                if(method_exists($arrayClass[0], $arrayClass[1])) {
                    $this->auth_class = $arrayClass[0];
                    $this->auth_method = $arrayClass[1];
                    $this->auth_expected = $data['auth']['expected'] ?? null;
                    if (isset($data['auth']['redirect'])) $this->auth_redirect = $data['auth']['redirect'];
                }else throw new UndefinedRouteMethodException($name, $arrayClass[1]);
            } else throw new UndefinedRouteClassException($name);
        }
    }

    /**
     * Find parameter of route
     */
    private function findParameters() {
        $tempArrayURL = explode('/', $this->url);
        $arrayURL = array();
        $count = null;
        // Clean URL user
        foreach (array_filter($tempArrayURL, function($a) {
            return !empty($a);
        }) as $key => $value) $arrayURL[] = $value;
        // Search parameters
        foreach ($arrayURL as $item) {
            // Check if parameters is present
            if(preg_match('/^{[a-zA-Z0-9]*}$/', $item)) {
                $this->parameters[] = $item;
            }
        }
    }

    /**
     * Load class of route
     * @return WebPage
     * @throws ClassNotExtendsControllerException
     */
    public function load(): WebPage {
        if(is_null($this->webpage)) {
            $class = new $this->class();
            if(is_subclass_of($class, 'System\Controller')) $this->webpage = new $this->class(); else throw new ClassNotExtendsControllerException(get_class($class), $this->url);
        }
        return $this->webpage;
    }

    /**
     * Get url of route with parameters
     * @param array ...$params parameters to url
     * @return string
     * @throws TooFewParametersException
     * @throws TooManyParametersException
     * @throws IncorrectParameterRouteException
     */
    public function getUrl(...$params): String {
        if(sizeof($params) === 1 && is_array($params[0])) $params = $params[0];
        if(($need = sizeof($this->parameters)) < ($given = sizeof($params))) throw  new TooManyParametersException($this->name, $need, $given);
        if(($need = sizeof($this->parameters)) > ($given = sizeof($params))) throw  new TooFewParametersException($this->name, $need, $given);
        if(is_string($this->url)) $url = $this->url; else $url = "";
        for($i = 0; $i < sizeof($this->parameters); $i++) {
            $parameter = $this->parameters[$i];
            $param = substr($parameter, 1, strlen($parameter)-2);
            // Check param with regex defined in routing file
            if(isset($this->regex[$param])) {
                $regex = $this->regex[$param];
                if(!preg_match("/^$regex$/", $params[$i])) throw new IncorrectParameterRouteException($this->url, $param);
            }
            $url = preg_replace("/$parameter/", $params[$i], $url);
        }
        return $url;
    }

    /**
     * Compare route with url given
     * @param String $url browser url
     * @return array|null list of parameters
     * @throws IncorrectParameterRouteException
     */
    public function checkURL(String $url): ?array {
        $tempArrayRoute = explode('/', $this->url);
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
                if(isset($this->regex[$param])) {
                    $regex = $this->regex[$param];
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
     * Launch action of route
     * @param array $parameters parameters to method
     * @return Response Response of web page
     * @throws ClassNotExtendsControllerException
     * @throws \Exception
     */
    public function action(array $parameters): Response {
        if(!is_null($this->auth_class) && !is_null($this->auth_method)) {
            $authClass = new $this->auth_class();
            $returnAuthValue = call_user_func(array($authClass, $this->auth_method));
            if($this->auth_expected != $returnAuthValue) {
                if(!is_null($this->auth_redirect) && !empty($this->auth_redirect)) {
                    $newRoute = Router::getInstance()->find($this->auth_redirect);
                    if(is_null($newRoute->parameters)) {
                        header('Location: ' . $newRoute->url);
                        exit;
                    }
                }
                return new Response();
            }
        }
        $this->load();
        return call_user_func_array(array($this->webpage, $this->method), $parameters);
    }

}