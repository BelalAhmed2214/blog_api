<?php

namespace Tests\Unit\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;

class CommentModelTest extends TestCase
{  
    use RefreshDatabase;
    
    public function test_comments_database_has_expected_columns(): void{
        $this->assertTrue( 
            Schema::hasColumns('comments', [
              'id','user_id','post_id', 'comment'
          ]),1);
    }
    
    public function test_user_has_many_comments(){
        $user = User::factory()->create();
        $comments = Comment::factory()->create(['user_id'=>$user->id]);

        $this->assertTrue($user->comments->contains($comments));        
        $this->assertEquals(1, $user->comments->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection',$user->comments);
    }

    public function test_comments_belongs_to_a_post(){
        $user = User::factory()->create(); 
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create(['post_id' => $post->id]);
        
        $this->assertEquals(61, $comment->post->count());
        $this->assertInstanceOf(Post::class, $comment->post);
    }
    
    public function test_comments_belongs_to_a_user(){
        $user = User::factory()->create(); 
        $comments = Comment::factory()->create(['user_id' => $user->id]);
        $this->assertEquals(115, $comments->user->count());
        $this->assertInstanceOf(User::class, $comments->user);
    }
    
}
