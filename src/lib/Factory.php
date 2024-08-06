<?php

namespace PhpCliTools;

class Factory {
  /**
   * @return Config
   */
  static public function makeConfig($argv) {
    return new Config($argv);
  }

  static public function makeRunner($runnerId) {
    if ($runnerId === "copy") {
      return new CopyRunner();
    } elseif ($runnerId == "minify") {
      return new MinifyRunner();
    } elseif ($runnerId == "revision") {
      return new RevisionRunner();
    } elseif ($runnerId == "less2css") {
      return new Less2CssRunner();
    } elseif ($runnerId == "sass2css") {
      return new Sass2CssRunner();
    }
    return null;
  }
}
