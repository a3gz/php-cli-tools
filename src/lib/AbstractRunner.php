<?php

namespace PhpCliTools;

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
  protected function getDestination() {
    $r = '';
    if (isset($this->config['to'])
      && is_string($this->config['to'])
    ) {
      $r = trim($this->config['to']);
      if (substr($r, -1) !== '/') {
        $r .= '/';
      }
    }
    return $r;
  }

  /**
   * @return string
   */
  abstract public function getId();

  /**
   * @return string
   */
  protected function getSource() {
    $r = '';
    if (isset($this->config['from']) && is_string($this->config['from'])) {
      $r = trim($this->config['from']);
      if (substr($r, -1) !== '/') {
        $r .= '/';
      }
    }
    return $r;
  }

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