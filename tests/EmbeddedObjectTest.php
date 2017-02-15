<?php

use Tests\Gallery;
use Tests\Image;
use Illuminate\Support\Collection;

class EmbeddedObjectTest extends MongoTestCase
{

	/** @test */
	public function we_can_store_and_hydrate_embedded_object_and_collection()
	{
		$gallery = $this->buildObject();
		$mapper = $this->mapper($gallery);
		$mapper->store($gallery);
		$this->seeInDatabase('galleries', [
			'name' => 'my gallery',
			'featured' => [
				'url' => 'image C',
			],
			'images' => [
				[
					'url' => 'image A',
				],
				[
					'url' => 'image B',
				],
			],
		]);
		$loadedGallery = $mapper->find($gallery->getId());
		$this->assertInstanceOf(Collection::class, $loadedGallery->getImages());
		$this->assertCount(2, $loadedGallery->getImages());
		$this->assertInstanceOf(Image::class, $loadedGallery->getFeaturedImage());
		$this->assertEquals('image C', $loadedGallery->getFeaturedImage()->getUrl()); 
	}

	protected function buildObject()
	{
		$imageA = new Image('image A');
		$imageB = new Image('image B');
		$imageC = new Image('image C');
		$gallery = new Gallery('my gallery');
		$gallery->addImage($imageA);
		$gallery->addImage($imageB);
		$gallery->setFeaturedImage($imageC);
		return $gallery;
	}
}