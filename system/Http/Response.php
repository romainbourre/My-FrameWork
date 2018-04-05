<?php

namespace System\Http;

/**
 * Class Response
 * Response of web page
 * @package System
 * @author Romain Bourré
 */
class Response {

    /**
     * Body content of response
     * @var null
     */
    private $_content = null;

    public function __construct(String $content = null) {
        if(!is_null($content)) $this->_content = $content;
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
       $content = "";
       if(!is_null($this->_content)) $content .= $this->content();
       return $content;
    }

}