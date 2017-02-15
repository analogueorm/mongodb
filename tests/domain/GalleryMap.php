<?php

namespace Tests;

use Analogue\MongoDB\EntityMap;

class GalleryMap extends EntityMap
{
	protected $arrayName = null;

	protected $properties = [
		'_id',
		'name',
		'featured',
		'images',
	];	

	public function images(Gallery $gallery)
	{
		return $this->embedsMany($gallery, Image::class);
	}

	public function featured(Gallery $gallery)
	{
		return $this->embedsOne($gallery, Image::class);
	}



}