<?php

namespace PhpCliTools;

use lessc;

class Less2CssRunner extends AbstractRunner {
  /**
   * @var array
   */
  private $formatters = [
    'expanded' => 'classic',
    'compressed' => 'compressed',
    'lessjs' => 'lessjs',
  ];

  /**
   * @return string
   */
  private function getFormatter() {
    $r = $this->formatters['compressed'];
    if (
      isset($this->config['formatter'])
      && isset($this->formatters[$this->config['formatter']])
    ) {
      $r = $this->formatters[$this->config['formatter']];
    }
    return $r;
  }

  public function getId() {
    return 'less2css';
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

    $files = scandir($src);
    foreach ($files as $inputFile) {
      $fullSourceFile = "{$src}/{$inputFile}";
      if (in_array($inputFile, ['.', '..']) || is_dir($fullSourceFile)) continue;
      $ext = pathinfo($inputFile, PATHINFO_EXTENSION);
      if ($ext !== 'less') {
        continue;
      }
      if (is_readable($fullSourceFile)) {
        $outputBaseName = str_replace('.less', "{$suffix}.css", $inputFile);
        $outputFilename = "{$tgt}/{$outputBaseName}";

        if (!is_dir($tgt)) {
          if ($this->isVerbose()) {
            $msg = "Create dir: {$tgt}";
            Console::log($msg);
          }
          mkdir($tgt, 0755, true);
        }

        if ($this->isVerbose()) {
          $msg = "\tCompile: {$inputFile} as {$outputBaseName}";
          Console::log($msg);
        }

        // We MUST create an instance of lessc class for every file we process,
        // otherwise repeatedly importing a common file will fail after the
        // first @import.
        $less = new lessc();
        $less->setImportDir([$src]);
        $less->setFormatter($this->getFormatter());

        $output = $less->compile('@import "' . $inputFile . '";');
        file_put_contents($outputFilename, $output);
      }
    }
  }
}
