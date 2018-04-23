<?php

namespace System\Modules\Database\Exceptions;

/**
 * Class DatabaseInvalidParamException
 * Raise error when found invalid parameter in Database configuration
 * @package System\Modules\Database\Exceptions
 * @author Romain Bourré
 */
class DatabaseInvalidParamException extends \Exception {

    /**
     * DatabaseInvalidParamException constructor.
     * @param string $param
     * @param string $confFile
     */
    public function __construct(string $param, string $confFile) {
        parent::__construct("Invalid parameter '$param' in application configuration file '$confFile'");
    }

}