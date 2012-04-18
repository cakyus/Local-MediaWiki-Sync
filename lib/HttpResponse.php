<?php

class HttpResponse {
	
	private $header;
	private $body;
	
	public function __construct() {
		$this->header = '';
		$this->body = '';
	}
	
	public function setHeader($responseText) {
		$this->header = $responseText;
	}
	
	public function setBody($responseText) {
		$this->body = $responseText;
	}
	
	public function getHeader() {
		return $this->header;
	}
	
	public function getBody() {
		return $this->body;
	}
	
	public function __toString() {
		return $this->body;
	}
}
