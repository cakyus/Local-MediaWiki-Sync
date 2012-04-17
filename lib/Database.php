<?php

class Database {

	private $config;

	public function __construct() {

		$this->config = new StdClass;
		$this->config->filename = 'data/Database.json';

		if (is_file($this->config->filename) == false) {
			$this->config->data = new StdClass;
			file_put_contents($this->config->filename, '{}');
		} else {
			$this->config->data = json_decode(
				file_get_contents($this->config->filename)
				);
			if (empty($this->config->data)) {
				throw new Exception('unable to decode the file');
			}
		}
	}
	
	public function __get($name) {
		return $this->config->data->$name;
	}
	
	public function __set($name, $value) {
		$this->config->data->$name = $value;
	}
	
	public function __toString() {
		return json_encode($this->config->data);
	}
	
	public function save() {
		return file_put_contents(
			  $this->config->filename
			, $this->__toString()
			);
	}
}
