<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests;

use LogicException;
use ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\Post;
use ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\User;
use ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\Photo;
use ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\Comment;
use ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\Profile;
use ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\SoftPost;
use ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\SoftUser;
use ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\InvalidKid;
use ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\SoftProfile;

class IntegrationTest extends TestCase
{
    /**
     * Setup run before each test.
     *
     * @before
     */
    public function beforeSetup()
    {
        $this->setUpDatabaseConnection();

        $this->createSchema();
    }

    protected function createSchema()
    {
        $this->schema()->create('users', function ($table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('name')->nullable();
            $table->string('email');
        });

        $this->schema()->create('friends', function ($table) {
            $table->integer('user_id');
            $table->integer('friend_id');
        });

        $this->schema()->create('posts', function ($table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('user_id');
            $table->integer('parent_id')->nullable();
            $table->string('name');
        });

        $this->schema()->create('comments', function ($table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('post_id');
            $table->integer('user_id');
            $table->string('comment');
        });

        $this->schema()->create('photos', function ($table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->morphs('imageable');
            $table->string('name');
        });

        $this->schema()->create('invalid_kids', function ($table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->morphs('invalidable');
            $table->string('name');
        });

        $this->schema()->create('profiles', function ($table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('user_id');
        });
    }

    /**
     * Tear down run after each test.
     *
     * @after
     */
    public function afterTearDown()
    {
        $this->schema()->dropIfExists('users');
        $this->schema()->dropIfExists('friends');
        $this->schema()->dropIfExists('posts');
        $this->schema()->dropIfExists('comments');
        $this->schema()->dropIfExists('photos');
        $this->schema()->dropIfExists('invalid_kids');
        $this->schema()->dropIfExists('profiles');
    }

    public function testInvalidRelationshipThrowsException()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('invalid relationship(s) for cascading deletes');

        $user = User::create(['email' => 'user@example.com']);
        $user->setCascadeDeletes(['non_existing_relation']);

        $user->delete();
    }

    public function testInvalidRelationshipTypeThrowsException()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('/Relation type .* not handled/');

        $post = Post::create(['user_id' => 0, 'name' => 'First Post']);
        $post->setCascadeDeletes(['user']);

        $post->delete();
    }

    public function testNotAllRecordsDeletedThrowsException()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Only deleted [0] out of [1] records');

        $user = User::create(['email' => 'user@example.com']);
        $post = $user->permanentPosts()->create(['name' => 'First Post']);
        $user->setCascadeDeletes(['permanentPosts']);

        $user->delete();
    }

    public function testDeletesCascadeFirstLevel()
    {
        $user = User::create(['email' => 'user@example.com']);
        $user->photos()->create(['name' => 'Avatar 1']);
        $user->photos()->create(['name' => 'Avatar 2']);
        $friend = $user->friends()->create(['email' => 'friend@example.com']);
        $post = $user->posts()->create(['name' => 'First Post']);
        $user->comments()->create(['post_id' => $post->id, 'comment' => 'First Comment']);
        $user->comments()->create(['post_id' => $post->id, 'comment' => 'Second Comment']);

        $this->assertEquals(7, User::count() + Photo::count() + Post::count() + Comment::count());

        $user->delete();

        $this->assertEquals(1, User::count());
        $this->assertEquals(1, User::count() + Photo::count() + Post::count() + Comment::count());
    }

    public function testDeletesCascadeSecondLevel()
    {
        $user = User::create(['email' => 'user@example.com']);
        $post = $user->posts()->create(['name' => 'First Post']);
        $post->photos()->create(['name' => 'Hero 1']);
        $post->photos()->create(['name' => 'Hero 2']);
        $childPost = $post->childPosts()->create(['user_id' => $user->id, 'name' => 'First Child Post']);
        $post->comments()->create(['user_id' => 0, 'comment' => 'First Comment']);
        $post->comments()->create(['user_id' => 0, 'comment' => 'Second Comment']);

        $this->assertEquals(7, User::count() + Photo::count() + Post::count() + Comment::count());

        $user->delete();

        $this->assertEquals(0, User::count() + Photo::count() + Post::count() + Comment::count());
    }

    public function testDeletesCascadeLowerLevels()
    {
        $user = User::create(['email' => 'user@example.com']);
        $post = $user->posts()->create(['name' => 'First Post']);
        $childPost = $post->childPosts()->create(['user_id' => 0, 'name' => 'First Child Post']);
        $grandchildPost = $childPost->childPosts()->create(['user_id' => 0, 'name' => 'First Grandchild Post']);
        $greatGrandchildPost = $grandchildPost->childPosts()->create(['user_id' => 0, 'name' => 'First Great Grandchild Post']);

        $this->assertEquals(5, User::count() + Post::count());

        $user->delete();

        $this->assertEquals(0, User::count() + Post::count());
    }

    public function testEntireTransactionIsRolledBack()
    {
        $user = User::create(['email' => 'user@example.com']);
        $post = $user->posts()->create(['name' => 'First Post']);
        $post->photos()->create(['name' => 'Hero 1']);
        $post->photos()->create(['name' => 'Hero 2']);
        $invalidKid = $post->invalidKids()->create(['name' => 'First Invalid Kid']);
        $post->comments()->create(['user_id' => 0, 'comment' => 'First Comment']);
        $post->comments()->create(['user_id' => 0, 'comment' => 'Second Comment']);

        $this->assertEquals(7, User::count() + Photo::count() + Post::count() + Comment::count() + InvalidKid::count());

        try {
            $exceptionThrown = false;

            $user->delete();
        } catch (LogicException $e) {
            $exceptionThrown = true;
        }

        $this->assertEquals(7, User::count() + Photo::count() + Post::count() + Comment::count() + InvalidKid::count());
        $this->assertTrue($exceptionThrown);
    }

    public function testDeletesHiddenRelatedRecords()
    {
        $user = User::create(['email' => 'user@example.com']);
        $user->profile()->create([]);
        $user->profile()->create([]);

        $this->assertEquals(3, User::count() + Profile::count());

        $user->delete();

        $this->assertEquals(0, User::count() + Profile::count());
    }

    public function testDeletesOnlyRelatedRecords()
    {
        $user = User::create(['email' => 'user@example.com']);
        $user->profile()->create([]);
        $user->profile()->create([]);
        $user2 = User::create(['email' => 'user2@example.com']);
        $user2->profile()->create([]);

        $this->assertEquals(5, User::count() + Profile::count());

        $user->delete();

        $this->assertEquals(2, User::count() + Profile::count());
    }

    public function testSoftDeletesCascade()
    {
        $user = SoftUser::create(['email' => 'user@example.com']);
        $user->photos()->create(['name' => 'Avatar 1']);
        $user->photos()->create(['name' => 'Avatar 2']);
        $friend = $user->friends()->create(['email' => 'friend@example.com']);
        $post = $user->posts()->create(['name' => 'First Post']);
        $user->comments()->create(['post_id' => $post->id, 'comment' => 'First Comment']);
        $user->comments()->create(['post_id' => $post->id, 'comment' => 'Second Comment']);

        $this->assertEquals(7, SoftUser::count() + SoftPost::count() + Photo::count() + Comment::count());

        $user->delete();

        $this->assertEquals(1, SoftUser::count());
        $this->assertEquals(0, SoftPost::count());
        $this->assertEquals(1, SoftUser::count() + SoftPost::count() + Photo::count() + Comment::count());

        $this->assertEquals(2, SoftUser::withTrashed()->count());
        $this->assertEquals(1, SoftPost::withTrashed()->count());
        $this->assertEquals(3, SoftUser::withTrashed()->count() + SoftPost::withTrashed()->count() + Photo::count() + Comment::count());
    }

    public function testForcedSoftDeletesCascade()
    {
        $user = SoftUser::create(['email' => 'user@example.com']);
        $user->photos()->create(['name' => 'Avatar 1']);
        $user->photos()->create(['name' => 'Avatar 2']);
        $friend = $user->friends()->create(['email' => 'friend@example.com']);
        $post = $user->posts()->create(['name' => 'First Post']);
        $user->comments()->create(['post_id' => $post->id, 'comment' => 'First Comment']);
        $user->comments()->create(['post_id' => $post->id, 'comment' => 'Second Comment']);

        $this->assertEquals(7, SoftUser::count() + SoftPost::count() + Photo::count() + Comment::count());

        $user->forceDelete();

        $this->assertEquals(1, SoftUser::count());
        $this->assertEquals(0, SoftPost::count());
        $this->assertEquals(1, SoftUser::count() + SoftPost::count() + Photo::count() + Comment::count());

        $this->assertEquals(1, SoftUser::withTrashed()->count());
        $this->assertEquals(0, SoftPost::withTrashed()->count());
        $this->assertEquals(1, SoftUser::withTrashed()->count() + SoftPost::withTrashed()->count() + Photo::count() + Comment::count());
    }

    public function testSoftDeletesHiddenRelatedRecords()
    {
        $user = SoftUser::create(['email' => 'user@example.com']);
        $user->profile()->create([]);
        $user->profile()->create([]);

        $this->assertEquals(3, SoftUser::count() + SoftProfile::count());

        $user->delete();

        $this->assertEquals(0, SoftUser::count() + SoftProfile::count());
        $this->assertEquals(3, SoftUser::withTrashed()->count() + SoftProfile::withTrashed()->count());
    }

    public function testForcedSoftDeletesHiddenRelatedRecords()
    {
        $user = SoftUser::create(['email' => 'user@example.com']);
        $user->profile()->create([]);
        $user->profile()->create([]);

        $this->assertEquals(3, SoftUser::count() + SoftProfile::count());

        $user->forceDelete();

        $this->assertEquals(0, SoftUser::count() + SoftProfile::count());
        $this->assertEquals(0, SoftUser::withTrashed()->count() + SoftProfile::withTrashed()->count());
    }

    public function testForcedSoftDeletesMixedRelatedRecords()
    {
        $user = SoftUser::create(['email' => 'user@example.com']);
        $user->profile()->create([]);
        $user->profile()->first()->delete();

        $this->assertEquals(2, SoftUser::withTrashed()->count() + SoftProfile::withTrashed()->count());

        $user->profile()->create([]);

        $this->assertEquals(2, SoftUser::count() + SoftProfile::count());
        $this->assertEquals(3, SoftUser::withTrashed()->count() + SoftProfile::withTrashed()->count());

        $user->forceDelete();

        $this->assertEquals(0, SoftUser::count() + SoftProfile::count());
        $this->assertEquals(0, SoftUser::withTrashed()->count() + SoftProfile::withTrashed()->count());
    }

    public function testSoftDeletesTransactionIsRolledBack()
    {
        $user = SoftUser::create(['email' => 'user@example.com']);
        $post = $user->posts()->create(['name' => 'First Post']);
        $post->photos()->create(['name' => 'Hero 1']);
        $post->photos()->create(['name' => 'Hero 2']);
        $invalidKid = $post->invalidKids()->create(['name' => 'First Invalid Kid']);
        $post->comments()->create(['user_id' => 0, 'comment' => 'First Comment']);
        $post->comments()->create(['user_id' => 0, 'comment' => 'Second Comment']);

        $this->assertEquals(7, User::count() + Photo::count() + Post::count() + Comment::count() + InvalidKid::count());

        try {
            $exceptionThrown = false;

            $user->delete();
        } catch (LogicException $e) {
            $exceptionThrown = true;
        }

        $this->assertEquals(7, User::count() + Photo::count() + Post::count() + Comment::count() + InvalidKid::count());
        $this->assertTrue($exceptionThrown);
    }

    public function testForcedSoftDeletesTransactionIsRolledBack()
    {
        $user = SoftUser::create(['email' => 'user@example.com']);
        $post = $user->posts()->create(['name' => 'First Post']);
        $post->photos()->create(['name' => 'Hero 1']);
        $post->photos()->create(['name' => 'Hero 2']);
        $invalidKid = $post->invalidKids()->create(['name' => 'First Invalid Kid']);
        $post->comments()->create(['user_id' => 0, 'comment' => 'First Comment']);
        $post->comments()->create(['user_id' => 0, 'comment' => 'Second Comment']);

        $this->assertEquals(7, User::count() + Photo::count() + Post::count() + Comment::count() + InvalidKid::count());

        try {
            $exceptionThrown = false;

            $user->forceDelete();
        } catch (LogicException $e) {
            $exceptionThrown = true;
        }

        $this->assertEquals(7, User::count() + Photo::count() + Post::count() + Comment::count() + InvalidKid::count());
        $this->assertTrue($exceptionThrown);
    }
}
