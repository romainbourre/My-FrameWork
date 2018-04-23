<?php

namespace System\Exceptions;

/**
 * Class URLRouteNotFoundException
 * Raise error when url not founded in route
 * @package System\Exceptions
 * @author Romain Bourré
 */
class UndefinedRouteUrlException extends \Exception {

    /**
     * UndefinedRouteUrlException constructor.
     * @param string $routeName
     */
    public function __construct(string $routeName) {
        parent::__construct("Url not found in route '$routeName'");
    }

}