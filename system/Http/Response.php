<?php

namespace System\Http;

/**
 * Class Response
 * Response of web page
 * @package System
 * @author Romain Bourré
 */
class Response {

    public const HTTP_CODE_SUCCESS = 200;
    public const HTTP_CODE_TEMPORARY_REDIRECTION = 307;
    public const HTTP_CODE_PERMANENT_REDIRECTION = 308;
    public const HTTP_CODE_BAD_REQUEST = 400;
    public const HTTP_CODE_NOT_AUTH = 401;
    public const HTTP_CODE_DENIED = 403;
    public const HTTP_CODE_NOT_FOUND = 404;
    public const HTTP_CODE_INTERNAL_SERVER_ERROR = 500;

    /**
     * Body content of response
     * @var null
     */
    private $_content;

    /**
     * @var int http response code
     */
    private $_http_response_code;

    /**
     * @var array response header
     */
    private $_header;

    /**
     * Response constructor.
     * @param String $content content of response
     * @param int $httpCodeResponse http code return
     * @param array $header header of response
     */
    public function __construct(String $content = "", int $httpCodeResponse= self::HTTP_CODE_SUCCESS, array $header = array()) {
        $this->_content = $content;
        $this->_http_response_code = $httpCodeResponse;
        $this->_header = $header;
    }

    /**
     * If content is include in arguments, set the content of response
     * else get the content of response
     * @param String|null $content
     * @return null|String
     */
    public function content(String $content = null): ?String {
        if(is_null($content)) return $this->_content;
        $this->_content = $content;
        return null;
    }

    /**
     * Show response
     * @return string
     */
    public function __toString(): String {
        http_response_code($this->_http_response_code);
        foreach ($this->_header as $type => $value) header("$type:$value");
        return $this->_content;
    }

}