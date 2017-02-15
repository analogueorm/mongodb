<?php

namespace Tests;

use Illuminate\Support\Collection;

class Gallery
{

	protected $_id;

	protected $name;

	protected $featured;

	protected $images;

	protected $attributes = [];

	public function __construct($name)
	{
		$this->name = $name;
		$this->images = new Collection;
	}

	public function setFeaturedImage(Image $image)
	{
		$this->featured = $image;
	}

	public function addImage(Image $image)
	{
		$this->images->push($image);
	}

	public function getImages()
	{
		return $this->images;
	}

	public function getFeaturedImage()
	{
		return $this->featured;
	}

	public function getId()
	{
		return $this->_id;
	}


}