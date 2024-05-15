<?php

class CopyRunner extends AbstractRunner {
  public function getId() {
    return 'copy';
  }

  protected function copyDir($src, $tgt, $recursive = false, $level = 0) {
    if (substr($src, -1) !== '/') {
      $src .= '/';
    }
    if (substr($tgt, -1) !== '/') {
      $tgt .= '/';
    }
    $tab = str_repeat("\t", $level);
    if ($this->isVerbose()) {
      $msg = "{$tab}Copy Dir: {$src} ==> {$tgt}";
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
        $tab = str_repeat("\t", $level+1);
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

  public function run() {
    $specs = $this->getConfig();
    $src = $specs['src'];
    $tgt = $specs['dest'];
    $recursive = isset($specs['recursive'])
      ? boolval($specs['recursive'])
      : false;
    if (substr($tgt, -1) !== '/') {
      $tgt .= '/';
    }
    if (!is_dir($tgt)) {
      mkdir($tgt, 0755, true);
    }
    $rename = isset($specs['rename']) ? $specs['rename'] : false;
    try {
      if (is_file($src)) {
        $srcName = basename($src);
        $destName = $srcName;
        if ($rename !== false) {
          $destName = $rename;
        }
        $copied = copy($src, "{$tgt}{$destName}");
        if ($this->isVerbose()) {
          $msg = "Copy {$src} ==> {$tgt}{$destName}";
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
