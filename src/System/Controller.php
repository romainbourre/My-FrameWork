<?php

namespace System;

/**
 * Class Controller
 * Main controller of web page
 * @package System
 * @author Romain Bourré
 */
class Controller {

    /**
     * Web page url
     * @var null|String
     */
    private $url = null;

    /**
     * Path of web page
     * @var null|string
     */
    private $dirPath = null;

    /**
     * Controller constructor.
     * @param String $url
     */
    public function __construct(String $url) {
        $this->dirPath = ROOT . substr($path = str_replace("\\", "/", get_class($this)),0, strrpos($path, "/", -1));
        $this->url = $url;
    }

    /**
     * Get web url of web page
     * @return String
     */
    public function getLink(): String {
        return $this->url;
    }

    /**
     * Get path of web page
     * @return null|string
     */
    public function getPath() {
        return $this->dirPath;
    }

    /**
     * Recover view content
     * @param $view String
     * @param array|null $variables
     * @return String content of view
     */
    protected function render(String $view, array $variables = null): String {

        ob_start();

        if(!is_null($variables)) extract($variables);

        $path = explode(".", $view);
        if(sizeof($path) == 1) $view = $this->dirPath . "/Views/" . $path[0]; else $view = ROOT . 'WebPage/' . $path[0] . '/Views/' . $path[1];

        if(file_exists($view . '.php')) $view .= '.php';
        elseif (file_exists($view . '.html')) $view .= '.html';

        include($view);

        $content = ob_get_clean();

        return $content;

    }

}