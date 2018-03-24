<?php

namespace System\Modules\Database\Exceptions;

/**
 * Class DatabaseParamNotFoundException
 * Raise exception when parameter to Database not found in app configuration file
 * @package System\Modules\Database\Exceptions
 * @author Romain Bourré
 */
class DatabaseParamNotFoundException extends \Exception {

    public function __construct(string $parameter, String $confFile) {
        parent::__construct("Parameter '$parameter' not found in configuration file '$confFile'");
    }

}