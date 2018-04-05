<?php

namespace System\Exceptions;

/**
 * Class TooFewParametersException
 * Raise error when too many parameters giver to route
 * @package System\Exceptions
 * @author Romain Bourré
 */
class TooManyParametersException extends \Exception {

    public function __construct(String $routeName, int $need, int $given) {
        parent::__construct("Too many parameters given to route '$routeName', need $need but $given given");
    }

}