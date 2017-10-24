<?php

use Tests\Post;
use Tests\Comment;
use Illuminate\Support\Collection;
use ProxyManager\Proxy\ProxyInterface;
use Analogue\ORM\System\Proxies\CollectionProxy;

class BelongsToTest extends MongoTestCase
{
	/** @test */
	public function we_can_store_a_related_entity()
	{
		$post = analogue_factory(Post::class)->make();
		$comment = analogue_factory(Comment::class)->make();
		$comment->post = $post;
		$this->mapper($comment)->store($comment);
		$this->seeInDatabase('posts', [
			'title' => $post->title,
		]);
		$this->seeInDatabase('comments', [
			'text' => $comment->text,
			'post_id' => $post->_id
		]);
	}

	/** @test */
	public function we_can_eager_load_a_related_entity()
	{
		$post = analogue_factory(Post::class)->make();
		$comment = analogue_factory(Comment::class)->make();
		$comment->post = $post;
		$mapper = $this->mapper($comment);
		$mapper->store($comment);
		$loadedComment = $mapper->with('post')->where('_id','=',$comment->_id)->first();
		$this->assertInstanceOf(Post::class, $loadedComment->post);

	}

	/** @test */
	public function we_can_lazy_load_a_related_entity()
	{
		$post = analogue_factory(Post::class)->make();
		$comment = analogue_factory(Comment::class)->make();
		$comment->post = $post;
		$mapper = $this->mapper($comment);
		$mapper->store($comment);
		$this->clearCache();
		$loadedComment = $mapper->where('_id','=',$comment->_id)->first();
		$this->assertInstanceOf(Post::class, $loadedComment->post);
		$this->assertInstanceOf(ProxyInterface::class, $loadedComment->post);
		$this->assertEquals($post->title, $loadedComment->post->title);
	}

	/** @test */
	public function a_dirty_related_entity_is_updated_on_store()
	{
		$post = analogue_factory(Post::class)->make();
		$comment = analogue_factory(Comment::class)->make();
		$comment->post = $post;
		$mapper = $this->mapper($comment);
		$mapper->store($comment);

		$loadedComment = $mapper->where('_id','=',$comment->_id)->first();

		$loadedComment->post->title="New Title";
		$mapper->store($loadedComment);
		$this->seeInDatabase('posts', [
			'title' => "New Title",
		]);
	}

	/** @test */
	public function setting_a_relationship_attribute_to_null_set_foreign_keys_to_null()
	{
		$post = analogue_factory(Post::class)->make();
		$comment = analogue_factory(Comment::class)->make();
		$comment->post = $post;
		$mapper = $this->mapper($comment);
		$mapper->store($comment);

		$loadedComment = $mapper->where('_id','=',$comment->_id)->first();
		$loadedComment->post = null;
		$mapper->store($loadedComment);

		$this->seeInDatabase('comments', [
			'text' => $comment->text,
			'post_id' => null
		]);
		
	}
}