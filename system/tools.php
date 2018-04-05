<?php

/**
 * Get url to route name
 * @author Romain Bourré
 * @param String $nameRoute
 * @param array ...$params
 * @return String
 * @throws \System\Exceptions\IncorrectParameterRouteException
 * @throws \System\Exceptions\RouteNotFoundException
 * @throws \System\Exceptions\TooFewParametersException
 * @throws \System\Exceptions\TooManyParametersException
 * @throws \System\Exceptions\UndefinedRouteClassException
 * @throws \System\Exceptions\UndefinedRouteMethodException
 * @throws \System\Exceptions\UndefinedRouteUrlException
 */
function url(String $nameRoute, ...$params) {
    return \System\Router::getInstance()->find($nameRoute)->getUrl($params);
}

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
 * @throws \System\Exceptions\UndefinedRouteMethodException
 * @throws \System\Exceptions\UndefinedRouteUrlException
 */
function url_max(String $nameRoute, ...$params) {
    return 'http://' . $_SERVER['HTTP_HOST'] . \System\Router::getInstance()->find($nameRoute)->getUrl($params);
}