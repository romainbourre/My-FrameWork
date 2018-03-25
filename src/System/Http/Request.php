<?php

namespace System\Http;

/**
 * Class Request
 * Http request
 * @package System\Http
 * @author Romain Bourré
 */
class Request {

    /**
     * @var string Url request
     */
    private $url;

    /**
     * @var array header of request
     */
    private $header;

    /**
     * Request constructor.
     */
    public function __construct() {
        $this->url = substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], "?"));
        $this->header = apache_request_headers();
        if(isset($_POST) && !empty($_POST)) {
            $_SESSION['POST'] = $_POST;
            unset($_POST);
            header('Location: ' . $this->url);
            exit;
        }
        if(isset($_GET) && !empty($_GET)) {
            $_SESSION['GET'] = $_GET;
            unset($_GET);
            header('Location: ' . $this->url);
            exit;
        }
    }

    /**
     * Get secure $_POST or $_GET variable
     * @param String $variable name of variable
     * @return null|String content
     */
    public function get(String $variable): ?String {
        if(isset($_SESSION['POST'][$variable])) return htmlspecialchars($_SESSION['POST'][$variable]);
        else if(isset($_SESSION['GET'][$variable])) return htmlspecialchars($_SESSION['GET'][$variable]);
        return null;
    }

    /**
     * @return string url of request
     */
    public function getUrl(): string {
        return $this->url;
    }

    /**
     * @return array header of request
     */
    public function getHeader(): array {
        return $this->header;
    }

}