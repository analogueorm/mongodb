<?php

namespace Tests;

use Analogue\MongoDB\EntityMap;

class CommentMap extends EntityMap
{

	public function post(Comment $comment)
	{
		return $this->belongsTo($comment, Post::class);
	}
}