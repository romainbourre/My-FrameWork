<?php

namespace System\Exceptions;

/**
 * Class TooFewParametersException
 * Raise error when too few parameters giver to route
 * @package System\Exceptions
 * @author Romain Bourré
 */
class TooFewParametersException extends \Exception {

    public function __construct(string $routeName, int $need, int $given) {
        parent::__construct("Too few parameters given to route '$routeName', need $need but $given given");
    }

}