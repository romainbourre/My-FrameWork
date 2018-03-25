<?php

namespace System\Exceptions;

/**
 * Class HttpNotFoundException
 * Raise when route not founded
 * @package System\Exceptions
 * @author Romain Bourré
 */
class RouteNotFoundException extends \Exception {

    public function __construct(String $nameRoute) {
        parent::__construct("Route '$nameRoute' not found", 5565);
    }

}