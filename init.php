<?php
function __autoload($class) {
    $parts = explode('\\', $class);
    require 'lib/'.end($parts).'.php';
}
?>