<?php

use Tests\Post;

class MapperTest extends MongoTestCase
{
	/** @test */
	public function we_can_store_an_entity()
	{
		$post = analogue_factory(Post::class)->make();
		$this->mapper($post)->store($post);
		$this->seeInDatabase('posts', [
			'title' => $post->title,
		]);
	}

	/** @test */
	public function we_can_update_an_entity()
	{
		$post = analogue_factory(Post::class)->make();
		$this->mapper($post)->store($post);

		$post->title = 'awesome title';

		$this->mapper($post)->store($post);

		$this->seeInDatabase('posts', [
			'title' => 'awesome title',
		]);
	}

	/** @test */
	public function we_can_update_an_entity_from_a_query()
	{
		$post = analogue_factory(Post::class)->make();
		$this->mapper($post)->store($post);

		$loadedPost = $this->mapper(Post::class)->where('_id',$post->_id)->first();

		$loadedPost->title = 'awesome title';

		$this->mapper(Post::class)->store($loadedPost);

		$this->seeInDatabase('posts', [
			'title' => 'awesome title',
		]);
	}

	/** @test */
	public function we_can_query_an_entity_using_the_find_method()
	{
		$post = analogue_factory(Post::class)->make();
		$this->mapper($post)->store($post);

		$loadedPost = $this->mapper(Post::class)->find($post->_id);
		$this->assertEquals($loadedPost->_id, $post->_id);
	}

}