<?php

/**
 * HTTP request library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Http;
use Origin\Util\ArrayUtil;

/**
 * HTTP request library.
 *
 * Despite being a "web programming language", PHP provides no universal (i.e.
 * cross-SAPI) native data structures representing the data made available to
 * to the interpreter about the current HTTP request. Whilst some non-standard
 * methods for the Apache 2 DSO SAPI are available, no reportable progress on
 * standardisation has been made.
 *
 * Origin's HTTP request library provides a cleaner interface to PHP's messy
 * internals, handling the sourcing and arranging of information about the
 * request on your behalf.
 */
class Request {
    /**
     * HTTP method definition.
     *
     * @var string
     */
    const METHOD_CONNECT = 'CONNECT';
    
    /**
     * HTTP method definition.
     *
     * @var string
     */
    const METHOD_DELETE = 'DELETE';
    
    /**
     * HTTP method definition.
     *
     * @var string
     */
    const METHOD_GET = 'GET';
    
    /**
     * HTTP method definition.
     *
     * @var string
     */
    const METHOD_HEAD = 'HEAD';
    
    /**
     * HTTP method definition.
     *
     * @var string
     */
    const METHOD_OPTIONS = 'OPTIONS';
    
    /**
     * HTTP method definition.
     *
     * @var string
     */
    const METHOD_POST = 'POST';
    
    /**
     * HTTP method definition.
     *
     * @var string
     */
    const METHOD_PUT = 'PUT';
    
    /**
     * HTTP method definition.
     *
     * @var string
     */
    const METHOD_TRACE = 'TRACE';

    protected $cookies = array();
    protected $headers = array();
    protected $method = '';
    protected $post_data = '';
    protected $post_fields = array();
    protected $post_files = array();
    protected $query_data = array();
    protected $url = '';

    /**
     * Initialiser.
     *
     * @param string $url The base URL.
     * @param string $method The request method; one of the METHOD_* constants.
     */
    public function __construct($url, $method) {
        $this->setUrl($url);
        $this->setMethod($method);
    }

    /**
     * Add a cookie.
     *
     * @param string $name The cookie's name.
     * @param mixed $value The cookie's value.
     */
    public function addCookie($name, $value) {
        $this->cookies[$name] = $value;
    }

    /**
     * Add a header.
     *
     * @param string $name The header's name.
     * @param mixed $value The header's value.
     */
    public function addHeader($name, $value) {
        $this->headers[strtolower($name)] = $value;
    }

    /**
     * Add a POST field.
     *
     * @param string $name The field's name.
     * @param mixed $value The field's value.
     */
    public function addPostField($name, $value) {
        $this->post_fields[strtolower($name)] = $value;
    }

    /**
     * Add a POST file.
     *
     * @param string $name The field's name.
     * @param mixed $value The file's content.
     */
    public function addPostFile($name, $content) {
        $this->post_files[strtolower($name)] = $content;
    }

    /**
     * Add a key=>value pair to the query string.
     *
     * @param string $name The key-pair's key.
     * @param mixed $value The key-pair's value.
     */
    public function addQueryData($name, $value) {
        $this->query_data[$name] = $value;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getUrl() {
        return $this->url;
    }

    /**
     * Set raw POST data.
     *
     * @param string $data The raw POST data.
     */
    public function setPostData($data) {
        $this->post_data = $data;
    }

    /**
     * Set the request method.
     *
     * @param string $method The request method; one of the METHOD_* constants.
     */
    public function setMethod($method) {
        $this->method = $method;
    }

    /**
     * Set the request URL.
     *
     * @param string $url The base URL.
     */
    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * Make an HTTP request object for the current session.
     *
     * @return Request The request object.
     * @todo Figure out how to handle POST files
     */
    public static function make() {
        $url = $_SERVER['REQUEST_URI'];

        $query_data_index = strpos($url, '?');
        if ($query_data_index !== false) {
            $url = substr($_SERVER['REQUEST_URI'], 0, $query_data_index);
        }

        $result = new static($url, $_SERVER['REQUEST_METHOD']);

        foreach ($_COOKIE as $name => $value) {
            $result->addCookie($name, $value);
        }

        foreach (static::getHeaders() as $name => $value) {
            $result->addHeader($name, $value);
        }

        if (isset($HTTP_RAW_POST_DATA)) {
            $result->setPostData($HTTP_RAW_POST_DATA);
        }

        foreach ($_POST as $name => $value) {
            $result->addPostField($name, $value);
        }

        foreach ($_GET as $name => $value) {
            $result->addQueryData($name, $value);
        }

        return $result;
    }

    /**
     * Wrap-around for getallheaders().
     *
     * Up until very recently, there was no functionality in the standard
     * library for retrieving HTTP headers sent by the client. As the majority
     * of web servers will not benefit from this addition to the standard
     * library for some time, we provide an alternative approach.
     *
     * @return array<string, string> The headers.
     */
    public static function getHeaders() {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $additional_headers = array(
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_TYPE'   => 'Content-Type',
        );

        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) ===  'HTTP_') {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ',
                                                                 substr($key, 5)))));
                $headers[$name] = $value;
            } elseif (array_key_exists($key, $additional_headers)) {
                $headers[$additional_headers[$key]] = $value;
            }
        }

        return $headers;
    }
}
