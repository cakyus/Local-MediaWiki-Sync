<?php

class Nomads_Loader {
	
	public static function register() {
		return spl_autoload_register(array(__CLASS__, 'load'));	
	}

	public static function load($class) {
		$file = dirname(__DIR__).'/'.str_replace('_','/',$class).'.php';
		if (is_file($file)) {
			require_once($file);
		}
	}
}
