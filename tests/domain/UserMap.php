<?php

namespace Tests;

use Analogue\MongoDB\EntityMap;

class UserMap extends EntityMap {

	public function roles(User $user)
	{
		return $this->belongsToMany($user, Role::class);
	}

}
