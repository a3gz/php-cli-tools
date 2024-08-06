<?php

namespace PhpCliTools;

class RunnerValidator {
  static public function extractRunnerId($task) {
    $r = null;
    if (isset($task['runner'])) {
      $runner = trim($task['runner']);
      if (in_array($r, self::getValidRunnersList())) {
        $r = $runner;
      }
    }
    return $runner;
  }

  static private function getValidRunnersList() {
    return [
      CopyRunner::instance()->getId(),
      MinifyRunner::instance()->getId(),
      RevisionRunner::instance()->getId(),
      Less2CssRunner::instance()->getId(),
      Sass2CssRunner::instance()->getId(),
    ];
  }
}
