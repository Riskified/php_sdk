<?php
function __autoload($class) {
    if (strpos($class, 'Riskified\\') == 0) {
        $parts = explode('\\', $class);
        $file = __DIR__.'/../../'.join('/',$parts).'.php';
        if (is_file($file))
        require_once $file;
    }
}
