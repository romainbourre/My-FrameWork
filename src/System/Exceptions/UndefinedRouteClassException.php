<?php

namespace System\Exceptions;

/**
 * Class UndefinedClassInRouteException
 * Raise when no class are defined in routing configuration
 * @package System\Exceptions
 * @author Romain Bourré
 */
class UndefinedRouteClassException extends \Exception {

    public function __construct(String $routeName) {
        parent::__construct("Class undefined in routing of route '$routeName'", '001');
    }

}