<?php

namespace PhpCliTools;

class RevisionRunner extends AbstractRunner {
  /**
   * @return string
   */
  private function getFileName() {
    $r = 'php-cli-tools.revision';
    $fileName = isset($this->config['file'])
      ? trim($this->config['file'])
      : '';
    if (!empty($fileName)) {
      $r = $fileName;
    }
    return $r;
  }

  public function getId() {
    return 'revision';
  }

  public function run() {
    $tgt = $this->getDestination();
    $fileName = $this->getFileName();
    if (!is_dir($tgt)) {
      $created = mkdir($tgt, 0755, true);
      if ($this->isVerbose()) {
        $msg = "Create dir: {$tgt}";
        if ($created) {
          Console::log("{$msg} : OK");
        } else {
          Console::danger("{$msg} : FAILED");
        }
      }
    }
    $revision = time();
    $absFileName = "{$tgt}{$fileName}";
    if ($this->isVerbose()) {
      $msg = "Create revision: {$absFileName}";
      Console::log($msg);
    }
    file_put_contents($absFileName, $revision);
  }
}
