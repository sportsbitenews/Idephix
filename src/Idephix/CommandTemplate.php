<?php

/**
 * Command class template.
 * Define a new class using the following template.
 * NOTE: every "execute" method MUST contain a BasicOperations
 * argument named $target defaulted to null as last argument.
 * 
 * Remote operations are called on the $target argument. 
 * 
 * $name field is optional
 */

namespace Idephix;

class CommandTemplate {
    
    public $name;
    
    public function __construct($name = null){
        $this->name = $name;
    }
    
    /** @Description Command description goes here */
    public function execute($a, $b = null, BasicOperations $target = null) {
        echo $a;
        if($b !== null) echo $b;
    }
    
}