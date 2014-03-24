<?php
function __autoload($class) {
    $parts = explode('\\', $class);
    if (array_shift($parts) == 'Riskified') {
        array_unshift($parts, __DIR__);
        $file = join('/',$parts).'.php';
        if (is_file($file))
            require_once $file;
    }
    return true;
}