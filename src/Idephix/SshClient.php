<?php

namespace Idephix;

use PHPSeclib\Net\SFTP;
use PHPSeclib\Net\SSH2;
use PHPSeclib\Crypt\RSA;

/**
 * Represents an ssh connection with a remote server
 * through the phpseclib library.
 * Provides functionalities such as remote command
 * running, sudo remote command running, sftp put and get
 * file transfering.
 */
class SshClient {

    private $readFirst;
    private $proxy;
    private $proxy_sftp;
    private $params;
    private $host;
    private $key;

    /**
     * Constructor
     *
     * @param $host The string representating the host identifier.
     * @param $sshParams The array containing ssh parameters.
     */
    public function __construct($host, $sshParams) {
        $this->readFirst = true;
        $this->host = $host;
        $this->params = array_merge(array('user' => '',
            'sudo_password' => '',
            'private_key_file' => '',
            'private_key_file_pwd' => '',
            'ssh_port' => '22'), $sshParams);
        $this->key = new RSA();
        $this->key->setPassword($this->params['private_key_file_pwd']);
        $this->key->loadKey(file_get_contents($this->params['private_key_file']));
        $this->connect();
    }

    public function connect() {
        if (($this->proxy = new SSH2($this->host)) == false) {
            throw new \Exception("Unable to connect");
        }
        if (($this->proxy_sftp = new SFTP($this->host)) == false) {
            throw new \Exception("Unable to connect");
        }
        try {
            $this->proxy->login($this->params['user'], $this->key);
            $this->proxy_sftp->login($this->params['user'], $this->key);
        } catch (\Exception $e) {
            throw new \Exception("Unable to connect");
        }
    }

    public function exec($cmd) {
        return $this->proxy->exec($cmd);
    }

    public function sudo($cmd) {
        if ($this->readFirst) {
            $this->proxy->read('#' . $this->params['user'] . '@.*:~\$#', NET_SSH2_READ_REGEX);
            $this->readFirst = false;
        }
        $this->proxy->write("sudo " . $cmd . "\n");
        $output = $this->proxy->read('#password|' . $this->params['user'] . '@.*:~\$#', NET_SSH2_READ_REGEX);
        if (preg_match('#password#', $output)) {
            $this->proxy->write($this->params['sudo_password'] . "\n");
            $this->proxy->read('#' . $this->params['user'] . '@.*:~\$#', NET_SSH2_READ_REGEX);
        }
    }

    public function get($from, $to) {
        return $this->proxy_sftp->get($from, $to, NET_SFTP_LOCAL_FILE);
    }

    public function put($from, $to) {
        return $this->proxy_sftp->put($to, $from, NET_SFTP_LOCAL_FILE);
    }

    public function disconnect() {
        return $this->proxy->disconnect();
    }

}
