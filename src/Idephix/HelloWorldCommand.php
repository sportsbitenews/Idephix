<?php


namespace Idephix;

/**
 * Example Command. 
 */

class HelloWorldCommand{
    
    public $name;
    
    public function __construct($name){
        $this->name = $name;
    }
    
    public function execute($who = null, BasicOperations $target = null) {
        $res = $target->run("echod 'Hello World'");
        if(strpos($res, "not found") !== null){
            $target->run("touch 1");
            $target->sudo("touch 2");
            $target->get("2","2");
        }
        if($who !== null)
            echo "hello".$this->random().$who."\n";
        else
            echo "hi";
    }
    
    public function random(){
        return 4;
    }
   

}