<?php

namespace Tests;

use Analogue\MongoDB\EntityMap;

class RoleMap extends EntityMap {

	public function roles(Role $role)
	{
		return $this->belongsToMany($role, User::class);
	}


	public function creator(Role $role)
	{
		return $this->belongsTo($role, User::class);
	}
}
