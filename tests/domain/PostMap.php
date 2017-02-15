<?php

namespace Tests;

use Analogue\MongoDB\EntityMap;

class PostMap extends EntityMap
{
	public function comments(Post $post)
	{
		return $this->hasMany($post, Comment::class);
	}
}