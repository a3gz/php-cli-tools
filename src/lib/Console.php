<?php

namespace PhpCliTools;

// https://misc.flogisoft.com/bash/tip_colors_and_formatting
class Console {
  static public function danger() {
    $args = func_get_args();
    if (count($args)) {
      foreach ($args as $str) {
        echo "\033[91m{$str}\033[0m ";
      }
    }
    echo "\n";
  }

  static public function log() {
    $args = func_get_args();
    if (count($args)) {
      foreach ($args as $str) {
        echo "{$str} ";
      }
    }
    echo "\n";
  }

  static public function warning() {
    $args = func_get_args();
    if (count($args)) {
      foreach ($args as $str) {
        echo "\033[93m{$str}\033[0m ";
      }
    }
    echo "\n";
  }
}
