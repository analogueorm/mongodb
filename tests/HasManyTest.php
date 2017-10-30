<?php

use Tests\Post;
use Tests\Comment;
use Illuminate\Support\Collection;
use ProxyManager\Proxy\ProxyInterface;
use Analogue\ORM\System\Proxies\CollectionProxy;

class HasManyTest extends MongoTestCase
{
	/** @test */
	public function we_can_store_a_related_entity()
	{
		$post = analogue_factory(Post::class)->make();
		$comment = analogue_factory(Comment::class)->make();

		$post->comments = [$comment];

		$this->mapper($post)->store($post);
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
		$post->comments = [$comment];
		$mapper = $this->mapper($post);
		$this->mapper($post)->store($post);
		$this->clearCache();
		$loadedPost = $this->mapper($post)->with('comments')->where('_id','=',$post->_id)->first();
		$this->assertInstanceOf(Collection::class, $loadedPost->comments);

	}

	/** @test */
	public function we_can_lazy_load_a_related_entity()
	{
		$post = analogue_factory(Post::class)->make();
		$comment = analogue_factory(Comment::class)->make();
		$post->comments = [$comment];
		$mapper = $this->mapper($post);
		$this->mapper($post)->store($post);
		$this->clearCache();
		$loadedPost = $this->mapper($post)->where('_id','=',$post->_id)->first();

		$this->assertInstanceOf(Collection::class, $loadedPost->comments);
		$this->assertInstanceOf(ProxyInterface::class, $loadedPost->comments);
		$this->assertInstanceOf(CollectionProxy::class, $loadedPost->comments);
		$this->assertCount(1, $loadedPost->comments);
	}

	/** @test */
	public function a_dirty_related_entity_is_updated_on_store()
	{
		$post = analogue_factory(Post::class)->make();
		$comment = analogue_factory(Comment::class)->make();
		$post->comments = [$comment];
		$mapper = $this->mapper($post);
		$this->mapper($post)->store($post);

		$this->clearCache();
		$loadedPost = $this->mapper($post)->where('_id','=',$post->_id)->first();
		$loadedPost->comments->first()->text="New Comment";
		$mapper->store($loadedPost);
		$this->seeInDatabase('comments', [
			'text' => "New Comment",
			'post_id' => $post->_id
		]);
	}

	/** @test */
	public function setting_a_relationship_attribute_to_null_set_foreign_keys_to_null()
	{
		$post = analogue_factory(Post::class)->make();
		$comment1 = analogue_factory(Comment::class)->make();
		$comment2 = analogue_factory(Comment::class)->make();
		$post->comments = [$comment1, $comment2];
		$mapper = $this->mapper($post);
		$this->mapper($post)->store($post);
		$this->clearCache();

		$loadedPost = $this->mapper($post)->where('_id','=',$post->_id)->first();
		$loadedPost->comments = null;
		$mapper->store($loadedPost);
		$this->seeInDatabase('comments', [
			'text' => $comment1->text,
			'post_id' => null
		]);
		$this->seeInDatabase('comments', [
			'text' => $comment2->text,
			'post_id' => null
		]);
	}

	/** @test */
	public function saving_an_aggregate_with_relationships_does_not_detach_them()
	{
		$post = analogue_factory(Post::class)->make();
		$comment1 = analogue_factory(Comment::class)->make();
		$comment2 = analogue_factory(Comment::class)->make();
		$post->comments = [$comment1, $comment2];
		$mapper = $this->mapper($post);
		$this->mapper($post)->store($post);

		$this->clearCache();
		$loadedPost = $this->mapper($post)->where('_id','=',$post->_id)->first();
		$mapper->store($loadedPost);
		$this->seeInDatabase('comments', [
			'text' => $comment1->text,
			'post_id' => $post->_id,
		]);
		$this->seeInDatabase('comments', [
			'text' => $comment2->text,
			'post_id' => $post->_id,
		]);

		$this->clearCache();
		$loadedPost = $this->mapper($post)->with('comments')->where('_id','=',$post->_id)->first();
		$mapper->store($loadedPost);
		$this->seeInDatabase('comments', [
			'text' => $comment1->text,
			'post_id' => $post->_id,
		]);
		$this->seeInDatabase('comments', [
			'text' => $comment2->text,
			'post_id' => $post->_id,
		]);

		$this->clearCache();
		$loadedPost = $this->mapper($post)->where('_id','=',$post->_id)->first();
		$loadedPost->comments->map(function($comment) {
			$comment->text = "some non sense";
		});		
		$mapper->store($loadedPost);

		$this->seeInDatabase('comments', [
			'text' => "some non sense",
			'post_id' => $post->_id,
		]);

		$this->clearCache();
		$loadedPost = $this->mapper($post)->where('_id','=',$post->_id)->first();
		$id = $loadedPost->comments->first()->_id;
		$loadedPost->comments = $loadedPost->comments->filter(function($post) use ($id) {
			return $post->_id == $id;
		});
		$mapper->store($loadedPost);

		$this->seeInDatabase('comments', [
			'text' => "some non sense",
			'post_id' => $post->_id,
		]);
		$this->seeInDatabase('comments', [
			'text' => "some non sense",
			'post_id' => null,
		]);

	}
}