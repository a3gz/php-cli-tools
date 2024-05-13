<?php

class Factory {
  /**
   * @return Config
   */
  static public function makeConfig($argv) {
    return new Config($argv);
  }

  static public function makeRunner($runnerId) {
    if ($runnerId === "copy") {
      return new CopyRunner($params);
    } elseif ($runnerId == "minify") {
      return new MinifyRunner($params);
    } elseif ($runnerId == "revision") {
      return new RevisionRunner($params);
    } elseif ($runnerId == "less2css") {
      return new Less2CssRunner($params);
    } elseif ($runnerId == "scss2css") {
      return new Scss2CssRunner($params);
    }
    return null;
  }
}
