<?php

namespace System\Exceptions;

use Exception;

/**
 * Throw when the request precised on the route,
 * not corresponding with request
 * Class IncorrectRequestMethodException
 * @author Romain Bourré
 * @package System\Exceptions
 */
class IncorrectRequestMethodException extends Exception {

    public function __construct(string $find, string $attempt) {
        parent::__construct("Bad requested method, find '$find' but '$attempt' expected");
    }

}