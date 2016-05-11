<?php

/**
 * HTTP response library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Http;

class Response {
    /**
     * HTTP response body.
     *
     * @var string
     */
    protected $body = '';

    /**
     * HTTP response headers.
     *
     * @var array
     */
    protected $headers = array();

    /**
     * HTTP response status.
     *
     * @var string
     */
    protected $status;

    /**
     * HTTP response statuses.
     *
     * @var array<string, string|array<string>>
     */
    protected static $statuses = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',

        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Access Denied',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        420 => 'Enhance Your Calm',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => array(
            'Failed Dependency',
            'Method Failure',
        ),
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Failed',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'No Response',
        449 => 'Retry With',
        450 => 'Blocked By Windows Parental Controls',
        451 => array(
            'Unavailable For Legal Reasons',
            'Redirect'
        ),
        494 => 'Request Header Too Large',
        495 => 'Cert Error',
        496 => 'No Cert',
        497 => 'HTTP to HTTPS',
        499 => 'Client Closed Request',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        509 => 'Bandwith Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        598 => 'Network read timeout error',
        599 => 'Network connect timeout error',
    );

    /**
     * Initialiser.
     *
     * @param array $headers HTTP response headers.
     * @param string $body HTTP response body.
     */
    public function __construct($status, $headers, $body) {
        $this->setStatus($status);
        $this->setHeaders($headers);
        $this->setBody($body);
    }

    /**
     * Get the HTTP response body.
     *
     * @return string The HTTP response body.
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * Get the HTTP response headers.
     *
     * @return array The HTTP response headers.
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * Get the HTTP response status.
     *
     * @return integer The HTTP response status.
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set the HTTP response body.
     *
     * @param string $body HTTP response body.
     */
    public function setBody($body) {
        $this->body = $body;
    }

    /**
     * Set HTTP response headers.
     *
     * This method is incrediblr permissive and allows you to pass in almost any
     * combination of status codes and messages. All of the following variations
     * are considered valid statuses:
     *
     * - (string) "200 OK"
     * - (string) "OK"
     * - (integer) 200
     *
     * @param array $headers HTTP response headers.
     */
    public function setHeaders($headers) {
        $this->headers = $headers;
    }

    /**
     * Set the HTTP response status.
     *
     * @param integer|string $status HTTP response status.
     * @return boolean True on success, false otherwise.
     */
    public function setStatus($status) {
        if (is_string($status)) {
            list($code, $phrase) = explode(' ', $status, 2);
        } else {
            $code   = $status;
            $phrase = '';
        }

        if (!is_numeric($code)
                || !array_key_exists($status, static::$statuses)) {
            return false;
        }

        if (is_array(static::$statuses[$code])) {
            if ((strlen($phrase) === 0)
                    || !in_array($phrase, static::$statuses[$code])) {
                return false;
            }

            $this->status = "{$code} {$phrase}";
            return true;
        } else {
            if (strlen($phrase) > 0
                    && $phrase !== explode(' ', static::$statuses[$code], 2)[1]) {
                return false;
            }

            $this->status = static::$statuses[$code];
            return true;
        }

        return true;
    }
}
