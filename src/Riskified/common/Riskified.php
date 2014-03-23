<?php
function __autoload($class) {
	if (strpos($class, 'Riskified\\') == 0) {
		$file = end(explode('\\', $class)).'.php';
		foreach (new RecursiveDirectoryIterator(__DIR__.'/../', FilesystemIterator::SKIP_DOTS) as $dir => $info) {
			$path = $dir.'/'.$file;
            echo $path.PHP_EOL;
			if (is_file($path))
                require_once $path;
		}
	}
}