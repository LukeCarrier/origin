<?php

/**
 * View library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Routing;
use Origin\Http\Response as HttpResponse;

class Response implements IResponse {
    protected $status;
    protected $headers;
    protected $body;

    public function __construct($body, $status, $headers) {
        $this->setBody($body);
        $this->setStatus($status);
        $this->setHeaders($headers);
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setHeaders($headers) {
        $this->headers = $headers;
    }

    public function setBody($body) {
        $this->body = $body;
    }

    public function toHttpResponse() {
        return new HttpResponse($this->status, $this->headers, $this->body);
    }
}
