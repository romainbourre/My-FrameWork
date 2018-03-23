<?php

namespace System\Exceptions;

/**
 * Class ClassNotExtendsControllerException
 * Raise when web page not extend system Controller
 * @package System\Exceptions
 * @author Romain Bourré
 */
class ClassNotExtendsControllerException extends \Exception {

    public function __construct(String $class, String $route) {
        parent::__construct("The class '$class' of route '$route' not extend System\\Controller");
    }

}