<?php

class Nomads_Logger {
	
	public static function info($message) {
		echo date('H:i:s').' INFO '.$message."\n";
	}
	
	public static function warn($message) {
		echo date('H:i:s').' WARN '.$message."\n";
	}
}
