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
    private $uri;

    /**
     * @var array header of request
     */
    private $headers;

    /**
     * @var string Method of request
     */
    private $method;

    /**
     * Request constructor.
     */
    public function __construct() {
        if(!($endPos = strrpos($_SERVER['REQUEST_URI'], "?"))) $endPos = strlen($_SERVER['REQUEST_URI']);
        $this->uri = substr($_SERVER['REQUEST_URI'], 0, $endPos);
        $this->headers = apache_request_headers();
        $this->method = $_SERVER['REQUEST_METHOD'];
        if(!isset($_SESSION[md5($this->uri)])) {
            $_SESSION[md5($this->uri)] = array();
            $_SESSION[md5($this->uri)]['FILES'] = array();
        }
        $redirect = false;
        if(isset($_POST) && !empty($_POST)) {
            $_SESSION[md5($this->uri)] = array_merge($_SESSION[md5($this->uri)], $_POST);
            unset($_POST);
            $redirect = true;
        }
        if(isset($_GET) && !empty($_GET)) {
            $_SESSION[md5($this->uri)] = array_merge($_SESSION[md5($this->uri)], $_GET);
            unset($_GET);
            $redirect = true;
        }
        if(isset($_FILES) && !empty($_FILES)) {
            foreach ($_FILES as $k => $f) {
                $path = substr($f['tmp_name'], 0, strrpos($f['tmp_name'], "/", 0));
                if(move_uploaded_file($f['tmp_name'], $dest = "$path/file_" . md5($this->uri . time()))) $_FILES[$k]['tmp_name'] = $dest;
            }
            $_SESSION[md5($this->uri)]['FILES'] = array_merge($_SESSION[md5($this->uri)]['FILES'], $_FILES);
            unset($_FILES);
            $redirect = true;
        }
        if($redirect) {
            header("Location: $this->uri");
            exit;
        }
    }

    /**
     * Get POST, GET and FILES variable
     * @param string $variable name of variable
     * @param bool $secure security of return content
     * @return null|String|array content
     */
    public function get(string $variable, bool $secure = true) {
        $content = null;
        if(isset($_SESSION[md5($this->uri)][$variable])) $content = $_SESSION[md5($this->uri)][$variable];
        if(isset($_SESSION[md5($this->uri)]['FILES'][$variable])) $content = $_SESSION[md5($this->uri)]['FILES'][$variable];
        if(!$secure || is_null($content) || is_array($content)) return $content ; return htmlspecialchars($content);
    }

    /**
     * Get POST, GET or FILES variable and delete it
     * @param string $variable name of variable
     * @param bool $secure security of return content
     * @return null|string|array content
     */
    public function get_and_del(string $variable, bool $secure = true) {
        if(!is_null($content = $this->get($variable, $secure))) {
            unset($_SESSION[md5($this->uri)][$variable]);
            unset($_SESSION[md5($this->uri)]['FILES'][$variable]);
        }
        return $content;
    }

    /**
     * Unset GET, POST and FILES variable
     */
    public function purge(): void {
        foreach ($_SESSION[md5($this->uri)]['FILES'] as $f) {
            unlink($f['tmp_name']);
        }
        unset($_SESSION[md5($this->uri)]);
        $_SESSION[md5($this->uri)] = array();
        $_SESSION[md5($this->uri)]['FILES'] = array();
    }

    /**
     * @return string url of request
     */
    public function getUri(): string {
        return $this->uri;
    }

    /**
     * @return array header of request
     */
    public function getHeaders(): array {
        return $this->headers;
    }

    /**
     * @return string method of request
     */
    public function getMethod(): string {
        return $this->method;
    }

}