<?php

class HttpRequest {
	
	public $url;
	public $method;
	public $query;
	
	private $cookies;
	
	public function __construct() {
		$this->method = 'GET';
		$this->query = new stdClass;
		$this->cookies = dirname(
			dirname(__FILE__)
			).'/data/cookies.txt';
	}
	
	public function send() {
		
		$response = new HttpResponse;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookies);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookies);
		// curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_PROXY, '');
		
		if ($this->method == 'GET') {
			curl_setopt($ch, CURLOPT_URL, $this->url.'?'.http_build_query($this->query));
		} else {
			curl_setopt($ch, CURLOPT_URL, $this->url);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->query));
		}
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

/*
		if(array_key_exists('login', $this->_config) && $this->_config['login'] == 'digestauth') {
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
			curl_setopt($ch, CURLOPT_USERPWD, $this->_config['username'].':'.$this->_config['password']);
		}
*/

		$response->setBody(curl_exec($ch));
		
		return $response;
	}
}

