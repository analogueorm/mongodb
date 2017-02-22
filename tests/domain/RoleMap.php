<?php

namespace Tests;

use Analogue\MongoDB\EntityMap;

class RoleMap extends EntityMap {

	public function roles(User $user)
	{
		return $this->belongsToMany($user, Role::class);
	}

}
