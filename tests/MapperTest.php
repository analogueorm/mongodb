<?php

use Tests\Post;
use Tests\Simple;

class MapperTest extends MongoTestCase
{
	/** @test */
	public function we_can_use_a_custom_id_value()
	{
		$post = analogue_factory(Post::class)->make();
		$post->_id = str_random(32);
		$post->title = "Some title";
		$this->mapper($post)->store($post);
		$this->seeInDatabase('posts', [
			'_id' => $post->_id,
			'title' => "Some title",
		]);
	}

	/** @test */
	public function we_can_store_and_load_an_entity()
	{
		$post = analogue_factory(Post::class)->make();
		$post->title = "Some title";
		$this->mapper($post)->store($post);
		$this->seeInDatabase('posts', [
			'title' => "Some title",
		]);

		$loadedPost = $this->mapper($post)->find($post->_id);
		$this->assertEquals("Some title", $loadedPost->title);
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
	public function we_can_update_an_entity_with_new_field()
	{
		$post = analogue_factory(Post::class)->make();
		$this->mapper($post)->store($post);

		$post->description = 'awesome title';

		$this->mapper($post)->store($post);

		$this->seeInDatabase('posts', [
			'description' => 'awesome title',
		]);
	}

	/** @test */
	public function we_can_update_an_entity_with_new_array_field()
	{
		$post = analogue_factory(Post::class)->make();
		$this->mapper($post)->store($post);

		$post->description = ['some array'];

		$this->mapper($post)->store($post);

		$this->seeInDatabase('posts', [
			'description' => ['some array'],
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

	/** @test */
	public function id_column_is_hydrated_when_storing_plain_objects()
	{
		$simple = new Simple('simple');
		$mapper = $this->mapper($simple);
		$mapper->store($simple);
		$this->assertNotNull($simple->getId());
	}	

}