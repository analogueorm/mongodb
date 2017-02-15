<?php

namespace Tests;

use Analogue\MongoDB\EntityMap;

class SimpleMap extends EntityMap {

	protected $arrayName = null;

	protected $properties = [
		'_id',
		'name',
	];


}
