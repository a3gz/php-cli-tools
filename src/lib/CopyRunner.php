<?php

namespace PhpCliTools;

class CopyRunner extends AbstractRunner {
  const ALL_FILES = '*';

  protected function copyDir($src, $tgt, $recursive = false, $level = 0) {
    if (substr($src, -1) !== '/') {
      $src .= '/';
    }
    if (substr($tgt, -1) !== '/') {
      $tgt .= '/';
    }
    $tab = Console::tab($level);
    if ($this->isVerbose()) {
      $msg = "{$tab}Dir Copy: {$src} ==> {$tgt}";
      Console::log($msg);
    }

    $dir = scandir($src);
    foreach ($dir as $srcFileName) {
      if (in_array($srcFileName, ['.', '..'])) {
        continue;
      }
      $fullSrc = "{$src}{$srcFileName}";
      $fullTgt = "{$tgt}{$srcFileName}";

      if (is_dir($fullSrc) && $recursive) {
        $this->copyDir($fullSrc, $fullTgt, $recursive, $level + 1);
      }

      if (is_readable($fullSrc) && !is_dir($fullSrc)) {
        $tab = Console::tab($level+1);
        if (!is_dir($tgt)) {
          $created = mkdir($tgt, 0755, true);
          if ($this->isVerbose()) {
            $msg = "{$tab}Create dir: {$tgt}";
            if ($created) {
              Console::log("{$msg} : OK");
            } else {
              Console::danger("{$msg} : FAILED");
            }
          }
        }
        $copied = copy($fullSrc, $fullTgt);
        if ($this->isVerbose()) {
          $msg = "{$tab}Copy: {$fullSrc} ==> {$fullTgt}";
          if ($copied) {
            Console::log("{$msg} : OK");
          } else {
            Console::danger("{$msg} : FAILED");
          }
        }
      }
    }
  }

  /**
   * @return string
   */
  private function getFile() {
    $r = self::ALL_FILES;
    $file = isset($this->config['file']) ? trim($this->config['file']) : '';
    if (!empty($file)) {
      $r = $file;
    }
    return $r;
  }

  public function getId() {
    return 'copy';
  }

  private function getRename() {
    return isset($this->config['rename']) && is_string($this->config['rename'])
      ? trim($this->config['rename'])
      : false;
  }

  private function isRecursive() {
    return isset($this->config['recursive'])
      ? boolval($this->config['recursive'])
      : false;
  }

  public function run() {
    $cfg = $this->getConfig();
    $src = $this->getSource();
    $tgt = $this->getDestination();
    $singleFile = $this->getFile();
    $recursive = $this->isRecursive();
    if (substr($tgt, -1) !== '/') {
      $tgt .= '/';
    }
    if (!is_dir($tgt)) {
      mkdir($tgt, 0755, true);
    }
    $rename = $this->getRename();
    try {
      $fullSingleFile = "{$src}{$singleFile}";
      if (is_file($fullSingleFile)) {
        $srcName = $singleFile;
        $destName = $singleFile;
        if ($rename !== false) {
          $destName = $rename;
        }
        $copied = copy($fullSingleFile, "{$tgt}{$destName}");
        if ($this->isVerbose()) {
          $msg = "Copy {$fullSingleFile} ==> {$tgt}{$destName}";
          if ($copied) {
            Console::log("{$msg} : OK");
          } else {
            Console::danger("{$msg}: FAILED");
          }
        }
      } elseif (is_dir($src)) {
        $this->copyDir($src, $tgt, $recursive === true);
      } else {
        Console::danger("Not a file nor a directory: {\"src\": \"{$src}\"}");
      }
    } catch (\Exception $e) {
      Console::danger("Uncaught error: ", $e->getMessage());
    }
  }
}
