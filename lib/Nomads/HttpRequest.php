<?php

class Nomads_HttpRequest {
	
	public $url;
	public $method;
	public $query;
	public $proxy;
	
	// options
	public $isGetHeader;
	
	private $cookies;
	
	public function __construct() {
		// set default configurations
		$this->method = 'GET';
		$this->query = new stdClass;
		$this->cookies = sys_get_temp_dir().'/cookies.txt';
		$this->isGetHeader = true;
	}
	
	public function send() {
		
		$response = new Nomads_HttpResponse;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookies);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookies);
		
		if ($this->method == 'GET') {
			curl_setopt($ch, CURLOPT_URL, $this->url.'?'.http_build_query($this->query));
		} else {
			curl_setopt($ch, CURLOPT_URL, $this->url);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->query));
		}
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		if ($this->isGetHeader) {
			curl_setopt($ch, CURLOPT_HEADER, true);
		}
		
/*
		if(array_key_exists('login', $this->_config) && $this->_config['login'] == 'digestauth') {
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
			curl_setopt($ch, CURLOPT_USERPWD, $this->_config['username'].':'.$this->_config['password']);
		}
*/
		
		if ($this->isGetHeader) {
			
			$responses = explode("\r\n\r\n", curl_exec($ch), 2);

			$response->setHeader($responses[0]);
			
			if (isset($responses[1])) {
				$response->setBody($responses[1]);
			}
		} else {
			$response->setBody(curl_exec($ch));
		}
		
		return $response;
	}
}

