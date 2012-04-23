<?php

/**
Help your code to be more readable by using "forech" instead of "while"

@example
class myIterator extends Nomads_Iterator {
	private $position = 0;
	public function __construct() {
		parent::__construct();
	}
	protected function fetch() {
		if ($this->position == 0) {
			++$this->position;
			return '0k';
		}
		return false;
	}
}

$it = new myIterator;

foreach($it as $key => $value) {
    var_dump($key, $value);
}
**/

class Nomads_Iterator implements Iterator {

    private $position = 0;
    private $data = array();

    public function __construct() {
        $this->position = 0;
    }

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->data[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
		if (!isset($this->data[$this->position])) {
			$element = $this->fetch();
			if (!empty($element)){
				$this->data[] = $element;
			}
		}
        return isset($this->data[$this->position]);
    }
    
	protected function fetch() {
		return false;
	}
	
	public function add($element) {
		if (is_array($element)) {
			$this->data = array_merge($this->data, $element);
		} else {
			$this->data[] = $element;
		}
	}
}
