<?php

class Nomads_JsonHttpRequest extends Nomads_HttpRequest {
	
	public function __construct() {
		parent::__construct();
		$this->method = 'POST';
	}
	
	public function send() {
		
		if ($response = parent::send()) {
			return json_decode($response);
		}
		
		return false;
	}
}

