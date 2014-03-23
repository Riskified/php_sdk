<?php
function __autoload($class) {
	if (strpos($class, 'Riskified\\SDK') == 0) {
		$file = end(explode('\\', $class)).'.php';
		foreach (new RecursiveDirectoryIterator(__DIR__, FilesystemIterator::SKIP_DOTS) as $dir => $info) {
			$path = $dir.'/'.$file;
			if (is_file($path)) 
				require $path;
		}
	}
}