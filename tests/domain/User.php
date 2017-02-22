<?php

namespace Tests;

use Analogue\ORM\Entity;
use Illuminate\Support\Collection;

class User extends Entity
{
	public function __construct()
	{
		$this->roles = new Collection;
	}
}
