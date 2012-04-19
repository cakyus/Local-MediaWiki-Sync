<?php

class MediaWiki_Server {
  
	public $username;
	public $password;
	public $api;
	public $config;

	private $_api;
	private $_config;
  
	public function open() {
	  
		$request = new Nomads_JsonHttpRequest;
		$logger = new Nomads_Logger;

		$request->url = $this->api;
		$request->query->action = 'login';
		$request->query->format = 'json';
		$request->query->lgname = $this->username;
		$request->query->lgpassword = $this->password;

		$logger->info('Login to '.$request->url);
		
		if (!$response = $request->send()) {
			return false;
		}

		if ($response->login->result == 'NeedToken') {
			$request->query->lgtoken = $response->login->token;
			if (!$response = $request->send()) {
				return false;
			}
			if ($response->login->result == 'Success') {
				$logger->info('Logged as "'.$this->username.'"');
				return true;
			}
		}
		
		return false;
	}
	
	public function getPages() {
		
		$request = new Nomads_JsonHttpRequest;
		$logger = new Nomads_Logger;
		
		$request->url = $this->api;
		$request->query->action = 'query';
		$request->query->format = 'json';
		$request->query->list = 'allpages';
		$request->query->aplimit = 5000;
		
		/**
		 * @note 
		 * - you can use aplimit (maximum number of pages returned)
		 *   and apfrom (name of a page) to optimize the query
		 * 
		 * @example
		 * $request->query->aplimit = 3;
		 * $request->query->apfrom = "Main Page";
		 **/
		
		if (!$response = $request->send()) {
			return false;
		}
		
		return $response->query->allpages;
	}
  
  public function request($action, $params=FALSE) {
    $ch = curl_init();
    $cwd = dirname(__FILE__);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cwd . '/cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cwd . '/cookies.txt');

    if(is_array($params)) {
      curl_setopt($ch, CURLOPT_URL, $this->_api);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array_merge(array(
        'action' => $action,
        'format' => 'json'
      ), $params)));
    } else {
      $params = array(
        'action' => $action,
        'format' => 'json'
      );
      curl_setopt($ch, CURLOPT_URL, $this->_api . '?' . http_build_query($params));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $this->_addAuth($ch);

    $json = curl_exec($ch);
    return json_decode($json);
  }

  public function getPage($revID) {
    $ch = curl_init();
    $cwd = dirname(__FILE__);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cwd . '/cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cwd . '/cookies.txt');
    curl_setopt($ch, CURLOPT_URL, $this->_config['root'] . '?oldid=' . $revID . '&action=raw');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $this->_addAuth($ch);
    return curl_exec($ch);
  }
  
  public function getPageByTitle($title) {
    $ch = curl_init();
    $cwd = dirname(__FILE__);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cwd . '/cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cwd . '/cookies.txt');
    curl_setopt($ch, CURLOPT_URL, $this->_config['root'] . '?title=' . $title . '&action=raw');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $this->_addAuth($ch);
    return curl_exec($ch);
  }

  public function getPageStatusByTitle($title) {
    $ch = curl_init();
    $cwd = dirname(__FILE__);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cwd . '/cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cwd . '/cookies.txt');
    curl_setopt($ch, CURLOPT_URL, $this->_config['root'] . '?title=' . $title . '&action=raw');
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $this->_addAuth($ch);
    $content = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($code == 404) {
      return 404;
    }

    if(preg_match('/#REDIRECT \[\[(.+)\]\]/', $content, $match)) {
      return $match[1];
    }
    
    return $code;
  }
  
  public function getRecentChanges() {
    $ch = curl_init();
    $cwd = dirname(__FILE__);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cwd . '/cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cwd . '/cookies.txt');
    curl_setopt($ch, CURLOPT_URL, $this->_config['root'] . '?title=Special:RecentChanges&feed=atom');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $this->_addAuth($ch);
    return curl_exec($ch);
  }
  
  private function _addAuth(&$ch) {
    if(array_key_exists('login', $this->_config) && $this->_config['login'] == 'digestauth') {
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
      curl_setopt($ch, CURLOPT_USERPWD, $this->_config['username'].':'.$this->_config['password']);
    }
  }
  
  public function uploadFile($name, $filename) {
    $response = $this->request('query', array('titles'=>$name, 'prop'=>'info', 'intoken'=>'edit'));

    if($response && property_exists($response->query, 'pages')) {
      
      $pages = $response->query->pages;
      $page = get_object_vars($pages);
      $page = array_pop($page);
      $token = $page->edittoken;

      $ch = curl_init();
      $cwd = dirname(__FILE__);
      curl_setopt($ch, CURLOPT_COOKIEFILE, $cwd . '/cookies.txt');
      curl_setopt($ch, CURLOPT_COOKIEJAR, $cwd . '/cookies.txt');
  
      curl_setopt($ch, CURLOPT_URL, $this->_api);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        'action' => 'upload',
        'format' => 'json',
        'filename' => $name,
        'file' => '@' . $filename,
        'ignorewarnings' => 1,
        'token' => $token
      ));
  
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  
      $this->_addAuth($ch);
  
      $json = curl_exec($ch);
      return json_decode($json);
    }
  }
  
  public function editPage($pageTitle, $text) {
    $response = $this->request('query', array('titles'=>$pageTitle, 'prop'=>'info', 'intoken'=>'edit'));
    print_r($response);
    echo "\n";
    
    if($response && property_exists($response->query, 'pages')) {
      
      $pages = $response->query->pages;
      $page = get_object_vars($pages);
      $page = array_pop($page);
      $token = $page->edittoken;
        
      /* 
       * MediaWiki provides some ways to prevent editing conflicts
       
          basetimestamp  - Timestamp of the base revision (gotten through prop=revisions&rvprop=timestamp).
                           Used to detect edit conflicts; leave unset to ignore conflicts.
          starttimestamp - Timestamp when you obtained the edit token.
                           Used to detect edit conflicts; leave unset to ignore conflicts
       */
      $response = $this->request('edit', array('title'=>$pageTitle, 'text'=>$text, 'summary'=>'--changed-in-dropbox--', 'token'=>$token));
      print_r($response);
      echo "\n";
      
      return TRUE;
    }
    
    return FALSE;
  }
}
