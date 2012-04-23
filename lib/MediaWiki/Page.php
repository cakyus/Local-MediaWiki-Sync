<?php

class MediaWiki_Page {
	
	public $id;
	public $namespace;
	public $title;
	
	private $server;
	
	public function __construct(MediaWiki_Server $server = null) {
		if (!empty($server)) {
			$this->server = $server;
		}
	}
	
	public function getRevisions() {
		
	}
	
	/**
	 * Get last revision of a page
	 * @return MediaWiki_Revision
	 **/
	
	public function getLastRevision() {
		
		$revision = new MediaWiki_Revision($this);
		$request = new Nomads_JsonHttpRequest;
		
		$request->url = $this->server->api;
		$request->query->format = 'json';		
		$request->query->action = 'query';
		$request->query->prop = 'revisions';
		$request->query->titles = $this->title;
		$request->query->rvprop = 'ids|timestamp|user|userid|size|comment|parsedcomment|content|tags';
		
		if (!$response = $request->send()) {
			return false;
		}
		
		if ($this->title == 'WikiAPBN:SPM Penggantian Uang Persediaan') {
			var_dump($response); //die();
		}
		
		$p = current($response->query->pages);
		$r = current($p->revisions);
		
		$revision->id = $r->revid;
		$revision->parentId = $r->parentid;
		$revision->timestamp = $r->timestamp;
		$revision->user = $r->user;
		$revision->userId = $r->userid;
		$revision->size = $r->size;
		$revision->comment = $r->comment;
		$revision->parsedComment = $r->parsedcomment;
		$revision->tags = $r->tags;
		$revision->content = end($r);
		
		return $revision;
	}
}
