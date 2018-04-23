<?php

namespace System\Exceptions;

/**
 * Class ClassNotExtendsControllerException
 * Raise when web page not extend system Controller
 * @package System\Exceptions
 * @author Romain Bourré
 */
class ClassNotExtendsControllerException extends \Exception {

    /**
     * ClassNotExtendsControllerException constructor.
     * @param string $class
     * @param String $route
     */
    public function __construct(string $class, String $route) {
        parent::__construct("The class '$class' of route '$route' not extend System\\Controller");
    }

}