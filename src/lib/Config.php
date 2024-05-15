<?php
class Config {
  /**
   * @var array
   */
  protected $data;

  public function __construct($argv) {
    array_shift($argv);
  
    $configFileName = $argv[0];
    if (!is_readable($configFileName)) {
      throw new \Exception("Configuration file in unreachable: {$configFileName}");
    }
  
    $this->data = json_decode(file_get_contents($configFileName), true);
  }

  public function get() {
    return $this->data;
  }
}
