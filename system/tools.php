<?php

/**
 * Get url to route name with server address
 * @author Romain Bourré
 * @param String $nameRoute
 * @param array ...$params
 * @return string
 * @throws \System\Exceptions\IncorrectParameterRouteException
 * @throws \System\Exceptions\RouteNotFoundException
 * @throws \System\Exceptions\TooFewParametersException
 * @throws \System\Exceptions\TooManyParametersException
 * @throws \System\Exceptions\UndefinedRouteClassException
 * @throws \System\Exceptions\UndefinedRouteFuncException
 * @throws \System\Exceptions\UndefinedRouteUrlException
 * @throws Exception
 */
function url(String $nameRoute, ...$params): string {
    return 'http://' . $_SERVER['HTTP_HOST'] . \System\Router::getInstance()->find($nameRoute)->getUrl($params);
}

/**
 * Load file to html
 * @param string $file path of file
 * @return string url
 */
function asset(String $file): string {
    return 'http://' . $_SERVER['HTTP_HOST'] . "/assets/$file";
}

/**
 * Get POST, GET of FILE variable
 * @param string $var name of variable
 * @param bool $secure security activation
 * @return null|array|string content of var
 */
function get(string $var, bool $secure = true) {
    return \System\Http\Request::getOnAll($var, $secure);
}

/**
 * Get application variables
 * @param string $name
 * @return mixed|null
 */
function vars(string $name) {
    if(isset(\System\System::get()->getVars()[$name])) return \System\System::get()->getVars()[$name];
    return null;
}