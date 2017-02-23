<?php

use Carbon\Carbon;
use Tests\Post;
use MongoDB\BSON\UTCDateTime;

class CastingTest extends MongoTestCase
{
	/** @test */
	public function we_can_cast_carbon_dates()
	{
		$now = Carbon::now();
		$post = new Post;
		$post->updated = $now;
		$post->array = [
			'date' => $now,
		];
		$post->subarray = [
			[
				'date' => $now,
			],
		];

		$this->mapper($post)->store($post);

		$results = $this->db()->collection('posts')->get();
		$row = $results->first();
		$this->assertInstanceOf(MongoDB\BSON\UTCDateTime::class, $row['updated']);
		$this->assertInstanceOf(MongoDB\BSON\UTCDateTime::class, $row['array']['date']);
		$this->assertInstanceOf(MongoDB\BSON\UTCDateTime::class, $row['subarray'][0]['date']);

		$loadedPost = $this->mapper($post)->find($post->_id);
		$this->assertInstanceOf(Carbon::class, $loadedPost->updated);
		$this->assertInstanceOf(Carbon::class, $loadedPost->array['date']);
		$this->assertInstanceOf(Carbon::class, $loadedPost->subarray[0]['date']);
	}


}