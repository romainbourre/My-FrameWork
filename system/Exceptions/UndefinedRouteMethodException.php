<?php

namespace System\Exceptions;

use Throwable;

/**
 * Class UndefinedRouteMethodException
 * Raise when a method in table routing doesn't found
 * @package System\Exceptions
 * @author Romain Bourré
 */
class UndefinedRouteMethodException extends \Exception {

    public function __construct(String $routeName, String $methodName) {
        parent::__construct("Undefined method '$methodName' in route '$routeName'", '002');
    }

}