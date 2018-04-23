<?php

namespace System\Exceptions;

/**
 * Class HttpNotFoundException
 * Raise when route not founded
 * @package System\Exceptions
 * @author Romain Bourré
 */
class RouteNotFoundException extends \Exception {

    /**
     * RouteNotFoundException constructor.
     * @param String $nameRoute
     */
    public function __construct(string $nameRoute) {
        parent::__construct("Route '$nameRoute' not found", 5565);
    }

}