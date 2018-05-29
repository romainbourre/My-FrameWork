<?php

namespace System\Exceptions;

use Exception;

/**
 * Class IncorrectAuthenticationException
 * @package Exceptions
 * @author Romain Bourré
 */
class IncorrectAuthenticationException extends Exception {

    /**
     * IncorrectAuthenticationException constructor.
     * @param String $routeName name of route
     * @param String $method authentication method
     * @param String $found authentication value found
     * @param String $expected authentication value expected
     */
    public function __construct(String $routeName, String $method, String $found, String $expected) {
        parent::__construct("Incorrect authentication to route '$routeName' : '$method' expected '$expected', '$found' found");
    }

}