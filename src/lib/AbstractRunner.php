<?php

abstract class AbstractRunner {
  /**
   * @var array
   */
  protected $config;

  /**
   * @return string
   */
  abstract public function getId();

  /**
   * @return AbstractRunner
   */
  static public function instance() {
    static $o = null;
    if ($o === null) {
      $o = new static();
    }
    return $o;
  }

  private function isVerbose() {
    if (!isset($this->config)
      || empty($this->config)
      || !is_array($this->config)
      || !isset($this->config['verbose'])
    ) {
      return false;
    }
    return $this->config['verbose'] === true;
  }

  abstract public function run();

  protected function verbose($msg) {
    if ($this->isVerbose()) {
      Console::log($msg);
    }
  }

  /**
   * @param array $config
   */
  public function withConfig($config) {
    $clone = clone $this;
    $clone->config = $config;
    return $clone;
  }
}