<?php

class HttpResponse {
	
	private $body;
	
	public function setBody($responseText) {
		$this->body = $responseText;
	}
	
	public function getBody() {
		return $this->body;
	}
	
	public function __toString() {
		return $this->body;
	}
}
