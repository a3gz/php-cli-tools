<?php

abstract class AbstractRunner {
  /**
   * @var array
   */
  protected $config;

  /**
   * @return array
   */
  protected function getConfig() {
    return $this->config;
  }

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

  protected function isVerbose() {
    $config = $this->config;
    if (!isset($config)
      || empty($config)
      || !is_array($config)
      || !isset($config['verbose'])
    ) {
      return false;
    }
    return $config['verbose'] === true;
  }

  abstract public function run();

  /**
   * @param Config $config
   */
  public function withConfig($config) {
    $clone = clone $this;
    $clone->config = $config;
    return $clone;
  }
}