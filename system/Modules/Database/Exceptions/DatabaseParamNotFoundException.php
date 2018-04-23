<?php

namespace System\Modules\Database\Exceptions;

/**
 * Class DatabaseParamNotFoundException
 * Raise exception when parameter to Database not found in app configuration file
 * @package System\Modules\Database\Exceptions
 * @author Romain Bourré
 */
class DatabaseParamNotFoundException extends \Exception {

    /**
     * DatabaseParamNotFoundException constructor.
     * @param string $parameter
     * @param string $confFile
     */
    public function __construct(string $parameter, string $confFile) {
        parent::__construct("Parameter '$parameter' not found in configuration file '$confFile'");
    }

}