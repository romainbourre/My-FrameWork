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
     * Define session name to variables
     */
    private const SESSION_VAR = 'VARS';

    /**
     * @var string Url request
     */
    private $uri;

    /**
     * @var string md5 converted uri
     */
    private $encrypt_uri;

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
        $this->encrypt_uri = $this->uri;
        $this->headers = apache_request_headers();
        $this->method = $_SERVER['REQUEST_METHOD'];
        if(!isset($_SESSION[self::SESSION_VAR][$this->encrypt_uri])) {
            $_SESSION[self::SESSION_VAR][$this->encrypt_uri] = array();
            $_SESSION[self::SESSION_VAR][$this->encrypt_uri]['FILES'] = array();
        }
        $redirect = false;
        if(isset($_POST) && !empty($_POST)) {
            $_SESSION[self::SESSION_VAR][$this->encrypt_uri] = array_merge($_SESSION[self::SESSION_VAR][$this->encrypt_uri], $_POST);
            unset($_POST);
            $redirect = true;
        }
        if(isset($_GET) && !empty($_GET)) {
            $_SESSION[self::SESSION_VAR][$this->encrypt_uri] = array_merge($_SESSION[self::SESSION_VAR][$this->encrypt_uri], $_GET);
            unset($_GET);
            $redirect = true;
        }
        if(isset($_FILES) && !empty($_FILES)) {
            foreach ($_FILES as $k => $f) {
                $path = substr($f['tmp_name'], 0, strrpos($f['tmp_name'], "/", 0));
                if(move_uploaded_file($f['tmp_name'], $dest = "$path/file_" . md5($this->uri . time()))) $_FILES[$k]['tmp_name'] = $dest;
            }
            $_SESSION[self::SESSION_VAR][$this->encrypt_uri]['FILES'] = array_merge($_SESSION[self::SESSION_VAR][$this->encrypt_uri]['FILES'], $_FILES);
            unset($_FILES);
            $redirect = true;
        }
        if($redirect) {
            echo new Response("", Response::HTTP_CODE_PERMANENT_REDIRECTION);
        }
    }

    /**
     * Get POST, GET and FILES variable
     * @param string $var name of variable
     * @param bool $secure security of return content
     * @return null|String|array content
     */
    public function get(string $var, bool $secure = true) {
        $content = null;
        if(isset($_SESSION[self::SESSION_VAR][$this->encrypt_uri][$var])) $content = $_SESSION[self::SESSION_VAR][$this->encrypt_uri][$var];
        if(isset($_SESSION[self::SESSION_VAR][$this->encrypt_uri]['FILES'][$var])) $content = $_SESSION[self::SESSION_VAR][$this->encrypt_uri]['FILES'][$var];
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
            unset($_SESSION[self::SESSION_VAR][$this->encrypt_uri][$variable]);
            unset($_SESSION[self::SESSION_VAR][$this->encrypt_uri]['FILES'][$variable]);
        }
        return $content;
    }

    /**
     * Reset POST, GET and FILES variables
     */
    public function resetVar(): void {
        $_SESSION[self::SESSION_VAR][$this->encrypt_uri] = array();
        $_SESSION[self::SESSION_VAR][$this->encrypt_uri]['FILES'] = array();
    }

    /**
     * Unset GET, POST and FILES variable
     */
    public function purge(): void {
        foreach ($_SESSION[self::SESSION_VAR] as $uri => $data) {
            if(isset($_SESSION[self::SESSION_VAR][$uri]['FILES'])) {
                foreach ($_SESSION[self::SESSION_VAR][$uri]['FILES'] as $f) {
                    unlink($f['tmp_name']);
                }
                unset($_SESSION[self::SESSION_VAR][$uri]['FILES']);
            }
            if (empty($_SESSION[self::SESSION_VAR][$uri])) unset($_SESSION[self::SESSION_VAR][$uri]);
        }
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

    /**
     * Get POST, GET and FILES variable in all request
     * @param string $var name of variable
     * @param bool $secure security of return content
     * @return null|String|array content
     */
    public static function getOnAll(string $var, bool $secure = true) {
        $content = null;
        foreach ($_SESSION[self::SESSION_VAR] as $url => $variables) {
            if(key_exists($var, $variables) && is_null($content)) $content = $variables[$var];
        }
        if(!$secure || is_null($content) || is_array($content)) return $content ; return htmlspecialchars($content);
    }

}