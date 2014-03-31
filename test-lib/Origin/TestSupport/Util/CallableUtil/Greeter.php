<?php

namespace Origin\TestSupport\Util\CallableUtil;

class Greeter {
    public function __construct($default_greeting='Hello, %s!') {
        $this->default_greeting = $default_greeting;
    }

    public function getDefaultGreeting() {
        return $this->default_greeting;
    }

    public function greet($person, $greeting=null) {
        $greeting = ($greeting === null) ? $this->default_greeting : $greeting;

        return sprintf($greeting, $person);
    }
}
