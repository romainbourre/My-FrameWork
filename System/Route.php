<?php

namespace System;

use System\Exceptions\ClassNotExtendsControllerException;
use System\Exceptions\IncorrectAuthenticationException;
use System\Exceptions\IncorrectFormatConfigurationFileException;
use System\Exceptions\IncorrectParameterRouteException;
use System\Exceptions\IncorrectRequestMethodException;
use System\Exceptions\TooFewParametersException;
use System\Exceptions\TooManyParametersException;
use System\Exceptions\UndefinedRouteClassException;
use System\Exceptions\UndefinedRouteFuncException;
use System\Exceptions\UndefinedRouteUrlException;
use System\Http\Request;
use System\Http\Response;

/**
 * Class Route
 * Represent route of routing System
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
     * @var null|string http method of route
     */
    private $method = null;

    /**
     * @var null class of route
     */
    private $class = null;

    /**
     * @var null $func of class
     */
    private $func = null;

    /**
     * @var mixed|null regex of parameters
     */
    private $regex = null;

    /**
     * @var array parameters of url
     */
    private $parameters = null;

    /**
     * @var String Class asked to authentication of routing
     */
    private $auth_class;

    /**
     * @var String Method asked to authentication of routing
     */
    private $auth_func;

    /**
     * @var mixed|null Expected value to authentication
     */
    private $auth_expected = null;

    /**
     * @var mixed|null Redirection route when authentication reject
     */
    private $auth_redirect = null;

    /**
     * @var Request|null loaded request
     */
    private $associated_request = null;

    /**
     * @var null|array loader parameters
     */
    private $associated_parameters = null;

    /**
     * Route constructor.
     * @param string $name
     * @param array $data
     * @throws UndefinedRouteClassException
     * @throws UndefinedRouteFuncException
     * @throws UndefinedRouteUrlException
     * @throws \Exception
     */
    public function __construct(string $name, array $data) {
        // NAM OF ROUTE
        $this->name = $name;
        // URL OF ROUTE
        if(isset($data['url'])) $this->url = $data['url']; else throw new UndefinedRouteUrlException($name);
        // CHECK VALIDITY OF ROUTE CLASS AND ROUTE FUNCTION
        if(isset($data['class'])) $action = $data['class']; else throw new UndefinedRouteClassException($name);
        $action = explode("::", $action);
        if(isset($action[0]) && class_exists($action[0])) {
            if(is_subclass_of($action[0], 'System\Controller')) {
                $this->class = $action[0];
            } else throw new ClassNotExtendsControllerException(get_class($action[0]), $this->url);
        } else throw new UndefinedRouteClassException($name);
        if(isset($action[1]) && method_exists($this->class, $action[1])) $this->func = $action[1]; else throw new UndefinedRouteFuncException($name, $action[1]);
        // GET PARAMETERS OF ROUTE
        if(isset($data['params'])) $this->regex = $data['params'];
        $this->findParameters();
        // CHECK AUTHENTICATION CONFIGURATION
        if(isset($data['auth'])) {
            if(!is_array($data['auth']) || !isset($data['auth']['class']) || empty($data['auth']['class'])) throw new IncorrectFormatConfigurationFileException(Router::ROUTING_CONF);
            $arrayClass = explode('::', $data['auth']['class']);
            if(sizeof($arrayClass) != 2) throw new IncorrectFormatConfigurationFileException(Router::ROUTING_CONF);
            if(class_exists($arrayClass[0])) {
                if(method_exists($arrayClass[0], $arrayClass[1])) {
                    $this->auth_class = $arrayClass[0];
                    $this->auth_func = $arrayClass[1];
                    $this->auth_expected = $data['auth']['expected'] ?? null;
                    if (isset($data['auth']['redirect'])) $this->auth_redirect = $data['auth']['redirect'];
                }else throw new UndefinedRouteFuncException($name, $arrayClass[1]);
            } else throw new UndefinedRouteClassException($name);
        }
        // GET HTTP REQUEST METHOD
        if(isset($data['method'])) {
            $this->method = $data['method'];
        }
    }

    /**
     * Find parameter of route
     */
    private function findParameters() {
        $this->parameters = array();
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
     * Get url of route with parameters
     * @param array ...$params parameters to url
     * @return string
     * @throws TooFewParametersException
     * @throws TooManyParametersException
     * @throws IncorrectParameterRouteException
     */
    public function getUrl(...$params): string {
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
     * Get http method request to a route
     * @return null|string
     */
    public function getHttpMethod(): ?string {
        return $this->method;
    }

    /**
     * Load request in route
     * @param Request $r Request to load
     * @return Route
     * @throws IncorrectParameterRouteException
     * @throws IncorrectRequestMethodException
     * @throws TooFewParametersException
     * @throws TooManyParametersException
     */
    public function load(Request $r): ?self {
        $url = $r->getUri();
        // PREPARE COMPONENTS OF URLs
        $urlComponentsRoute = explode('/', $this->url);
        $urlComponentsRequest = explode('/', $url);
        // CLEAN COMPONENTS
        $urlComponentsRoute = array_values(array_filter($urlComponentsRoute, function($a) {
            return !empty($a);
        }));
        $urlComponentsRequest = array_values(array_filter($urlComponentsRequest, function($a) {
            return !empty($a);
        }));
        // COMPARE SIZE OF COMPONENTS
        $sizeComparaison = ($sizeComponentsRequest = sizeof($urlComponentsRequest)) <=> ($sizeComponentRoute = sizeof($urlComponentsRoute));
        if($sizeComparaison === 1) return null;
        elseif ($sizeComparaison === -1) return null;
        // COMPARE COMPONENTS PARAMETERS
        $parameters = array();
        if(!empty($urlComponentsRequest) && !empty($urlComponentsRoute)) {
            for ($i = 0; $i < sizeof($urlComponentsRoute); $i++) {
                $routeComponent = $urlComponentsRoute[$i];
                $requestComponent = $urlComponentsRequest[$i];
                // EXTRACT PARAMETER IF EXIST
                if (preg_match('/^{[a-zA-Z0-9]*}$/', $routeComponent)) {
                    // EXTRACT NAME OF PARAMETER
                    $param = substr($routeComponent, 1, strlen($routeComponent) - 2);
                    // CHECK PARAMETER REGEX
                    if (isset($this->regex[$param])) {
                        $regex = $this->regex[$param];
                        if (!preg_match("/^$regex$/", $requestComponent)) throw  new IncorrectParameterRouteException($url, $param);
                    }
                    // SAVE PARAMETER
                    $parameters[$param] = $requestComponent;
                } else if ($routeComponent != $requestComponent) {
                    return null;
                }
            }
        }
        // COMPARE HTTP METHOD BETWEEN ROUTE AND REQUEST
        if(!is_null($r) && !is_null($this->method) && ($findMethod = $r->getMethod()) != ($attempt = $this->method)) throw new IncorrectRequestMethodException($findMethod, $attempt);
        $this->associated_request = $r;
        $this->associated_parameters = $parameters;
        return $this;
    }

    /**
     * Launch action of route
     * @return void Response of web page
     * @throws Exceptions\RouteNotFoundException
     * @throws IncorrectAuthenticationException
     * @throws UndefinedRouteClassException
     * @throws UndefinedRouteFuncException
     * @throws UndefinedRouteUrlException
     */
    public function exec(): void {
        if (!is_null($r = $this->associated_request) && is_array($p = $this->associated_parameters)) {
            if (!is_null($this->auth_class) && !is_null($this->auth_func)) {
                if ($this->auth_expected != ($authReturnedValue = call_user_func(array(new $this->auth_class(), $this->auth_func)))) {
                    if (!is_null($this->auth_redirect) && !empty($this->auth_redirect)) {
                        $redirection = Router::getInstance()->find($this->auth_redirect);
                        if (is_null($redirection->parameters) || empty($redirection->parameters)) {
                            echo new Response("", Response::HTTP_CODE_TEMPORARY_REDIRECTION, ['Location' => $redirection->url]);
                            return;
                        }
                    }
                    throw new IncorrectAuthenticationException($this->name, $this->auth_func, $authReturnedValue, $this->auth_expected);
                }
            }
            echo call_user_func_array(array(new $this->class, $this->func), array_merge(array($r), $p));
            return;
        }
    }

}