<?php

namespace PhpCliTools;

class MinifyRunner extends AbstractRunner {
  /**
   * @return string
   */
  private function calculateBuildNumber() {
    $r = '';
    if (
      isset($this->config['addBuildNumber'])
      && $this->config['addBuildNumber'] === true
    ) {
      $r = (string)(time() . rand(0, 100));
    }
    return $r;
  }

  public function getId() {
    return 'minify';
  }

  private function getSuffix() {
    $r = '';
    if (
      isset($suffix)
      && !empty($suffix)
    ) {
      $r = trim($suffix);
    }
    return $r;
  }

  /**
   * @return bool
   */
  private function isCreateTree() {
    return isset($this->config['create-tree'])
      && $this->config['create-tree'] === true;
  }

  /**
   * @param string $ext
   * @return bool
   */
  private function isSupportedFileType($ext) {
    return is_array($this->config['fileTypes'])
      && in_array($ext, $this->config['fileTypes']);
  }

  public function run() {
    $src = $this->getSource();
    $tgt = $this->getDestination();
    $suffix = $this->getSuffix();
    $buildNumber = $this->calculateBuildNumber();

    $files = scandir($src);
    foreach ($files as $inputFile) {
      $fullSourceFile = "{$src}{$inputFile}";
      if (in_array($inputFile, ['.', '..']) || is_dir($fullSourceFile)) {
        continue;
      }
      if (is_readable($fullSourceFile)) {
        $ext = pathinfo($inputFile, PATHINFO_EXTENSION);
        if (!$this->isSupportedFileType($ext)) {
          continue;
        } else {
          $bext = pathinfo($inputFile, PATHINFO_FILENAME);
          $outputBaseName = "{$bext}{$suffix}{$buildNumber}.{$ext}";
          if (!is_dir($tgt) && $this->isCreateTree()) {
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

          $fullOutputFile = "{$tgt}{$outputBaseName}";
          Console::log("Minify: {$fullSourceFile} ==> {$fullOutputFile}");
          try {
            $content = file_get_contents($fullSourceFile);
            $minifiedContent = $this->minify($content);
            file_put_contents($fullOutputFile, $minifiedContent);
          } catch (\Exception $e) {
            verbose("\t{$e->getMessage()}. Ignoring.\n");
          }
        }
      }
    }
  }

  private function minify($c) {
    $minified = preg_replace('#^\s*//.+$#m', "", $c);
    $ptrn1 = <<<'EOS'
(?sx)
# quotes
(
"(?:[^"\\]++|\\.)*+"
| '(?:[^'\\]++|\\.)*+'
)
|
# comments
/\* (?> .*? \*/ )
EOS;

    $ptrn2 = <<<'EOS'
(?six)
# quotes
(
"(?:[^"\\]++|\\.)*+"
| '(?:[^'\\]++|\\.)*+'
)
|
# ; before } (and the spaces after it while we're here)
\s*+ ; \s*+ ( } ) \s*+
|
# all spaces around meta chars/operators
\s*+ ( [*$~^|]?+= | [{};,>~+-] | !important\b ) \s*+
|
# spaces right of ( [ :
( [[(:] ) \s++
|
# spaces left of ) ]
\s++ ( [])] )
|
# spaces left (and right) of :
\s++ ( : ) \s*+
# but not in selectors: not followed by a {
(?!
(?>
    [^{}"']++
| "(?:[^"\\]++|\\.)*+"
| '(?:[^'\\]++|\\.)*+' 
)*+
{
)
|
# spaces at beginning/end of string
^ \s++ | \s++ \z
|
# double spaces to single
(\s)\s+
EOS;
    $minified = preg_replace("%$ptrn1%", '$1', $minified);
    $minified = preg_replace("%$ptrn2%", '$1$2$3$4$5$6$7', $minified);
    return $minified;
  }
}
