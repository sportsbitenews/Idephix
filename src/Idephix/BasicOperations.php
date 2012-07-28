<?php

namespace Idephix;

class BasicOperations {

    private $target;

    public function __construct(SshClient $tar) {
        $this->target = $tar;
    }

    public function run($cmd, $dryRun = false) {
        if (!$dryRun) {
            return $this->target->exec($cmd);
        } else {
            echo("Dry run: remote " . $cmd);
        }
    }

    public function local($cmd, $dryRun = false) {
        if (!$dryRun) {
            exec($cmd . ' 2>&1', $output, $return);
            if ($return != 0) {
                throw new \Exception("local: returned non-0 value: " . $return . "\n" . $output[0]);
            } else {
                return $output;
            }
        } else {
            echo("Dry run: local " . $cmd);
        }
    }

    public function sudo($cmd, $dryRun = false) {
        if (!$dryRun) {
            return $this->target->sudo($cmd);
        } else {
            echo("Dry run: remote sudo " . $cmd);
        }
    }

}

?>
