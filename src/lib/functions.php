<?php
function consoleLog($text) {
  echo $text;
}

function terminate($text) {
  echo "{$text}\n";
  exit(1);
}

function verbose($text, $verbose = true) {
  if ($verbose === true) {
    consoleLog($text);
  }
}
