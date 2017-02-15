<?php

namespace Tests;

use Analogue\MongoDB\EntityMap;

class ImageMap extends EntityMap
{
	protected $arrayName = null;
	
	protected $primaryKey = null;

	protected $properties = [
		'url'
	];

}
