<?php

class CopyRunner extends AbstractRunner {
  public function getId() {
    return 'copy';
  }

  protected function copyDir($src, $tgt, $recursive = false, $level = 1) {
    if (substr($src, -1) !== '/') {
      $src .= '/';
    }
    if (substr($tgt, -1) !== '/') {
      $tgt .= '/';
    }
    $dir = scandir($src);
    foreach ($dir as $srcFileName) {
      if (in_array($srcFileName, ['.', '..'])) {
        continue;
      }
      $fullSrc = "{$src}{$srcFileName}";
      $fullTgt = "{$tgt}{$srcFileName}";
  
      if (is_dir($fullSrc) && $recursive) {
        $this->copyDir($fullSrc, $fullTgt, $recursive, $level+1);
      }
  
      $tab = str_repeat("\t", $level);
      if (is_readable($fullSrc) && !is_dir($fullSrc)) {
        if (!is_dir($tgt)) {
          $this->verbose("\tCreate dir: {$tgt}");
          mkdir($tgt, 0755, true);
        }
        $this->verbose("{$tab}Copy {$fullSrc} ==> {$fullTgt}");
        copy($fullSrc, $fullTgt);
        $this->verbose("{$tab}OK");
      }
    }
  }
  
  public function run() {
    foreach ($this->config as $specs) {
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
          $this->verbose("Copy {$src} ==> {$tgt}{$destName}");
          copy($src, "{$tgt}{$destName}");
          $this->verbose("OK");
        } elseif (is_dir($src)) {
          if ($recursive === true) {
            $this->copyDir($src, $tgt, true);
          } else {
            $this->copyDir($src, $tgt);
          }
        } else {
          throw new \Exception('Invalid settings');
        }
      } catch(\Exception $e) {
        $this->verbose("\tfailed");
      }
    }    
  }
}
