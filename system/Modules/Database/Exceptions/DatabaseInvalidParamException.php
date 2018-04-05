<?php

namespace System\Modules\Database\Exceptions;

/**
 * Class DatabaseInvalidParamException
 * Raise error when found invalid parameter in Database configuration
 * @package System\Modules\Database\Exceptions
 * @author Romain Bourré
 */
class DatabaseInvalidParamException extends \Exception {

    public function __construct(string $param, String $confFile) {
        parent::__construct("Invalid parameter '$param' in application configuration file '$confFile'");
    }

}