<?php

namespace PhpCliTools;

use ScssPhp\ScssPhp\Compiler;

class Sass2CssRunner extends AbstractRunner {
  /**
   * @var array
   */
  private $formatters = [
    'compact' => '\ScssPhp\ScssPhp\Formatter\Compact',
    'compressed' => '\ScssPhp\ScssPhp\Formatter\Compressed',
    'crunched' => '\ScssPhp\ScssPhp\Formatter\Crunched',
    'expanded' => '\ScssPhp\ScssPhp\Formatter\Expanded',
    'nested' => '\ScssPhp\ScssPhp\Formatter\Nested',
  ];

  /**
   * @return string
   */
  private function getFormatter() {
    $r = $this->formatters['crunched'];
    if (
      isset($this->config['formatter'])
      && isset($this->formatters[$this->config['formatter']])
    ) {
      $r = $this->formatters[$this->config['formatter']];
    }
    return $r;
  }

  public function getId() {
    return 'sass2css';
  }

  private function getSuffix() {
    $r = '';
    if (
      isset($this->config['suffix'])
      && !empty($this->config['suffix'])
    ) {
      $r = trim($this->config['suffix']);
    }
    return $r;
  }

  public function run() {
    $cfg = $this->getConfig();
    $src = $this->getSource();
    $tgt = $this->getDestination();
    $suffix = $this->getSuffix();

    $scss = new Compiler();
    $scss->setImportPaths([$src]);
    $scss->setFormatter($this->getFormatter());

    $files = scandir($src);
    foreach ($files as $inputFile) {
      $fullSourceFile = "{$src}/{$inputFile}";
      if (in_array($inputFile, ['.', '..']) || is_dir($fullSourceFile)) continue;
      $ext = pathinfo($inputFile, PATHINFO_EXTENSION);
      if ($ext !== 'scss') {
        continue;
      }
      if (is_readable($fullSourceFile)) {
        $outputBaseName = str_replace('.scss', "{$suffix}.css", $inputFile);
        $outputFilename = "{$tgt}/{$outputBaseName}";

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

        if ($this->isVerbose()) {
          $msg = "Compile: {$inputFile} as {$outputBaseName}";
          Console::log($msg);
        }

        $output = $scss->compile('@import "' . $inputFile . '";');
        file_put_contents($outputFilename, $output);
      }
    }
  }
}
