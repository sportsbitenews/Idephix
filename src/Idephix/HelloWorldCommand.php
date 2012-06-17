<?php


namespace Idephix;

class HelloWorldCommand{
    
    public function execute($who = null) {
        $res = run("echod 'Hello World' > Hello1");
        if(strpos($res[0], "not found") !== null){
            $res = run("echo 'Ciao' > Hello2");
        }
        
            //local("cd ~ && mkdir nuova_directory");
        
        echo "ahah";
    }

}