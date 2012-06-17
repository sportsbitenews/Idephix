<?php

/**
 * Command class template.
 * Define a new class 
 * using the following template. 
 */

namespace Idephix;

class CommandTemplate {
    
    /** @Description Command description goes here */
    public function execute($a, $b = null) {
        $randomnumber = $this->random();
        if ($b !== null){
            echo($a.$b);
        } else {
            echo($a.$randomnumber);            
        }
        //$arglist = func_get_args();
    }
    
    public function random() {
        return 4;
    }
    

}