<?php

namespace Tests;

class Simple {

	protected $_id;

	protected $name;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function getId()
	{
		return $this->_id;
	}
}
