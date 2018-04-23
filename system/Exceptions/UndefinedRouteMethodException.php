<?php

namespace System\Exceptions;

/**
 * Class UndefinedRouteMethodException
 * Raise when a method in table routing doesn't found
 * @package System\Exceptions
 * @author Romain Bourré
 */
class UndefinedRouteMethodException extends \Exception {

    /**
     * UndefinedRouteMethodException constructor.
     * @param string $routeName
     * @param string $methodName
     */
    public function __construct(string $routeName, string $methodName) {
        parent::__construct("Undefined method '$methodName' in route '$routeName'", '002');
    }

}