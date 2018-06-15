<?php

namespace System\Exceptions;

/**
 * Class IncorrectFormatConfigurationFileException
 * Raise error when
 * @package System\Exceptions
 * @author Romain Bourré
 */
class IncorrectFormatConfigurationFileException extends \Exception {

    public function __construct(string $file) {
        parent::__construct("Incorrect format of configuration file '$file'");
    }

}