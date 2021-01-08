<?php
function log($text) {
  echo $text;
}

function terminate($text) {
  echo "{$text}\n";
  exit(1);
}

function verbose($text) {
  global $options;
  if ($options['--verbose'] === true) {
    log($text);
  }
}

// EOF
