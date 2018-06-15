<?php

namespace System\FrameworkWebPage;

use System\Controller;

class FrameworkController extends Controller {

    /**
     * FrameworkController constructor.
     */
    public function __construct() {
        $this->dirPath = ROOT . substr($path = str_replace("\\", "/", get_class($this)),0, strrpos($path, "/", -1));
    }

}