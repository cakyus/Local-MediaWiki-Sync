<?php

class JsonHttpRequest extends HttpRequest {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function send() {
		
		if ($response = parent::send()) {
			return json_decode($response);
		}
		
		return false;
	}
}

