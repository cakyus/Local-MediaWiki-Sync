<?php

class Logger {
	
	public function info($message) {
		echo date('H:i:s ').$message."\n";
	}
}
