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
        if(!($endPos = strrpos($_SERVER['REQUEST_URI'], "?"))) $endPos = strlen($_SERVER['REQUEST_URI']);
        $this->url = substr($_SERVER['REQUEST_URI'], 0, $endPos);
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
     * @param string $variable name of variable
     * @return null|String content
     */
    public function get(string $variable): ?string {
        if(isset($_SESSION['POST'][$variable])) return htmlspecialchars($_SESSION['POST'][$variable]);
        else if(isset($_SESSION['GET'][$variable])) return htmlspecialchars($_SESSION['GET'][$variable]);
        return null;
    }

    /**
     * Unset GET and POST variable
     */
    public function resetVar(): void {
        unset($_SESSION['POST']);
        unset($_SESSION['GET']);
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