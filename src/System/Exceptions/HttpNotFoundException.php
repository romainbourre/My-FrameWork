<?php

namespace System\Exceptions;

/**
 * Class HttpNotFoundException
 * Raise when page to url not founded
 * @package System\Exceptions
 * @author Romain Bourré
 */
class HttpNotFoundException extends \Exception {

    public function __construct(String $url) {
        parent::__construct("Page not found in url '$url'", 5565);
    }

}