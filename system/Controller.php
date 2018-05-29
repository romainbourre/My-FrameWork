<?php

namespace System;

use System\Http\Response;
use System\Interfaces\WebPage;

/**
 * Class Controller
 * Main controller of web page
 * @package System
 * @author Romain Bourré
 */
class Controller implements WebPage {

    /**
     * Path of web page
     * @var null|string
     */
    protected $dirPath = null;

    /**
     * Controller constructor.
     */
    public function __construct() {
        $this->dirPath = SRC . substr($path = str_replace("\\", "/", get_class($this)),0, strrpos($path, "/", -1));
    }

    /**
     * Get path of web page
     * @return null|string
     */
    public function getPath() {
        return $this->dirPath;
    }

    /**
     * Get application variable
     * @param string $name
     * @return null|mixed
     */
    public function getVar(string $name) {
        if(isset(System::get()->getVars()[$name])) return System::get()->getVars()[$name];
        return "";
    }

    /**
     * Recover view content
     * @param string $view
     * @param array|null $variables
     * @return String content of view
     */
    protected function render(string $view, array $variables = null): String {
        ob_start();
        if(!is_null($variables)) extract($variables);
        $path = explode(".", $view);
        if(sizeof($path) == 1) $view = $this->dirPath . "/Views/" . $path[0]; else $view = ROOT . 'WebPage/' . $path[0] . '/Views/' . $path[1];
        if(file_exists($view . '.php')) $view .= '.php';
        elseif (file_exists($view . '.html')) $view .= '.html';
        include($view);
        $content = ob_get_clean();
        return $content;
    }

    /**
     * Redirect to url
     * @param string $url
     * @param int $httpCodeResponse
     * @return Response
     */
    public function redirect(string $url, int $httpCodeResponse = Response::HTTP_CODE_SUCCESS): Response {
        return new Response("", $httpCodeResponse, ['Location' => $url]);
    }

    /**
     * Redirect to route
     * @param string $route name of route
     * @param int $httpCodeResponse
     * @param array $params parameters of route
     * @return Response
     * @throws Exceptions\IncorrectParameterRouteException
     * @throws Exceptions\RouteNotFoundException
     * @throws Exceptions\TooFewParametersException
     * @throws Exceptions\TooManyParametersException
     * @throws Exceptions\UndefinedRouteClassException
     * @throws Exceptions\UndefinedRouteFuncException
     * @throws Exceptions\UndefinedRouteUrlException
     */
    public function redirectToRoute(string $route, int $httpCodeResponse = Response::HTTP_CODE_SUCCESS, array $params = array()): Response {
        return new Response("", $httpCodeResponse, ['Location' => Router::getInstance()->find($route)->getUrl($params)]);
    }

}