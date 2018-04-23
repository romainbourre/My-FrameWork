<?php

namespace System\Exceptions;

/**
 * Class UndefinedClassInRouteException
 * Raise when no class are defined in routing configuration
 * @package System\Exceptions
 * @author Romain Bourré
 */
class UndefinedRouteClassException extends \Exception {

    /**
     * UndefinedRouteClassException constructor.
     * @param string $routeName
     */
    public function __construct(string $routeName) {
        parent::__construct("Class undefined in routing of route '$routeName'", '001');
    }

}