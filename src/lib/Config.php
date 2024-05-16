<?php
namespace PhpCliTools;

class Config {
  /**
   * @var array
   */
  protected $data;

  /**
   * @var array
   */
  protected $tasks;

  public function __construct($argv) {
    array_shift($argv);
  
    $configFileName = $argv[0];
    if (!is_readable($configFileName)) {
      throw new \Exception("Configuration file in unreachable: {$configFileName}");
    }
    $this->data = json_decode(file_get_contents($configFileName), true);

    $this->tasks = [];
    if (count($argv) > 1) {
      $tasks = explode(',', $argv[1]);
      foreach ($tasks as $task) {
        $task = trim($task);
        if (!empty($task)) {
          $this->tasks[] = $task;
        }
      }
    }
  }

  /**
   * @return array
   */
  public function getData() {
    return $this->data;
  }

  /**
   * @return array
   */
  public function getTasks() {
    return $this->tasks;
  }

  /**
   * @return bool
   */
  public function hasSpecificTasks() {
    return count($this->tasks) > 0;
  }
}
