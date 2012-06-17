<?php

namespace Idephix;


use PHPSeclib\Net\SSH2;
use PHPSeclib\Crypt\RSA;

class SshClient
{
    private $proxy;
    private $params;
    private $host;
    private $key;

    /**
     * Constructor
     *
     * @param array $options array('user', 'public_key_file', 'private_key_file', 'private_key_file_pwd', 'ssh_port')
     * @param Ssh2Proxy $proxy
     */
    public function __construct($proxy = null)
    {
//        $this->proxy = $proxy;
//
//        if (is_null($proxy)) {
//            $this->proxy = new PeclSsh2Proxy();
//        }
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function setParams($params)
    {
        $this->params = array_merge(array('user'                 => '',
                                          'public_key_file'      => '',
                                          'private_key_file'     => '',
                                          'private_key_file_pwd' => '',
                                          'ssh_port'             => '22'), $params);
        $this->key = new RSA();
        $this->key->setPassword($this->params['private_key_file_pwd']);
        $this-> key->loadKey(file_get_contents($this->params['private_key_file']));
    }

    /**
     *
     * @throws \Exception
     */
    public function connect()
    {
        if(($this->proxy = new SSH2($this->host)) == false){
            throw new \Exception("Unable to connect");
        }
        try {
            $this->proxy->login($this->params['user'], $this->key);
        }   catch (\Exception $e){
            throw new \Exception("Unable to connect");
        }
//        if (!$this->proxy->connect($this->host, $this->params['ssh_port'], $this->params['user'])) {
//            throw new \Exception("Unable to connect");
//        }
//
//        if (!$this->proxy->authByPublicKey($this->params['user'], $this->params['public_key_file'], $this->params['private_key_file'], $this->params['private_key_file_pwd'])) {
//            throw new \Exception("Unable to authenticate");
//        }
    }

    public function exec($cmd)
    {
        return $this->proxy->exec($cmd);
    }
    
    public function getLastError()
    {
        return $this->proxy->getLastError();
    }
}
