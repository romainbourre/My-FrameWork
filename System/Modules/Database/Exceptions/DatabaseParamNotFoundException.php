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
     */
    public function __construct(string $parameter) {
        parent::__construct("Parameter '$parameter' not found in application configurations files");
    }

}