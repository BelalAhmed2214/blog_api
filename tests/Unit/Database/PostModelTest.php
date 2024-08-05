<?php

namespace Tests\Unit\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;

class PostModelTest extends TestCase
{  
    use RefreshDatabase;
    
    public function test_posts_database_has_expected_columns(): void{
        $this->assertTrue( 
            Schema::hasColumns('posts', [
              'id','user_id','is_active', 'title', 'description'
          ]),1);
    }
    public function test_post_has_many_comments(){
        $user = User::factory()->create(); 
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comments = Comment::factory()->create(['post_id' => $post->id]);

        $this->assertTrue($post->comments->contains($comments));        
        $this->assertEquals(1, $post->comments->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection',$post->comments);
    }
    public function test_user_has_many_posts(){
        $user = User::factory()->create(); 
        $posts = Post::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->posts->contains($posts));        
        $this->assertEquals(1, $user->posts->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection',$user->posts);
    }
    public function test_posts_belongs_to_user(){
        $user = User::factory()->create();
        $posts = Post::factory()->create(['user_id'=>$user->id]);

        $this->assertEquals(114,$posts->user->count());
        $this->assertInstanceOf(User::class,$posts->user);
    }
    
    
}
