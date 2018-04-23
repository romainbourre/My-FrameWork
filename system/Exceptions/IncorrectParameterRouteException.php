<?php

namespace System\Exceptions;

/**
 * Class IncorrectParameterRouteException
 * Raise when a parameter in url browser is incorrect
 * @package System\Exceptions
 * @author Romain Bourré
 */
class IncorrectParameterRouteException extends \Exception {

    /**
     * IncorrectParameterRouteException constructor.
     * @param string $url
     * @param string $param
     */
    public function __construct(string $url, string $param) {
        parent::__construct("Parameter '$param' is incorrect in url '$url'. See 'routing.yml'.");
    }

}