<?php

class MediaWiki_Revision {
	
	public $id;             // The ID of the revision
	public $parentId;
	public $timestamp;      // The timestamp of the revision
	public $user;           // User that made the revision
	public $userId;         // User id of revision creator
	public $size;           // Length of the revision
	public $comment;        // Comment by the user for revision
	public $parsedComment;  // Parsed comment by the user for the revision
	public $content;        // Text of the revision
	public $tags;           // Tags for the revision
	
	public function __construct(MediaWiki_Page $page = null) {
		
	}
}
