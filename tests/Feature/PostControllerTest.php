<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use Tests\Traits\AuthTrait;

class PostControllerTest extends TestCase
{   
     use AuthTrait;
     public function test_posts_retrieved_successfully(): void{
   
        $this->loginAndSetToken('belal@gmail.com', '123456'); 
        $response = $this->getJson('/api/posts');
        $response->assertStatus(200);
     }
     public function test_post_retrieved_successfully_by_id():void{

        $this->loginAndSetToken('belal@gmail.com', '123456');
        $response = $this->getJson('api/posts/1');
        $response->assertStatus(200);
     }
     public function test_posts_stored_successfully():void{
        
       
        $this->loginAndSetToken('belal@gmail.com', '123456'); 
        $user = User::firstOrFail();
        // dd($user->json());
        $response = $this->postJson('api/posts',[
            'title'=>"post 61",
            'description'=>"post 61 desc",
            'is_active'=>0,
            'user_id'=>$user->id
        ]);
        $response->assertStatus(200);
     }
     public function test_posts_updated_successfully():void{
        
       
        $this->loginAndSetToken('belal@gmail.com', '123456'); 
        $response = $this->postJson('api/posts/1',[
            'title'=>"post 1",
            'description'=>"post 1 desc",
            'is_active'=>1,
        ]);
        $response->assertStatus(200);
     }
     public function test_posts_deleted_successfully():void{
        $this->loginAndSetToken('belal@gmail.com', '123456'); 
        $response = $this->deleteJson('api/posts/1');
        $response->assertStatus(200);
     }
     public function test_deleted_posts_retrieved_successfully():void{
        
        $this->loginAndSetToken('belal@gmail.com', '123456'); 
        $response = $this->getJson('/api/posts/deleted');
        $response->assertStatus(200);
     }
     public function test_restored_deleted_posts_successfully(): void{
        
        $this->loginAndSetToken('belal@gmail.com', '123456'); 
        $response = $this->postJson('/api/posts/restore/1');
        $response->assertStatus(200);
     }


     public function test_force_delete_posts_successfully():void{
    // Authenticate and get token
    $this->loginAndSetToken('belal@gmail.com', '123456');

    // Create a user if it doesn't exist
    $user = User::firstOrCreate(
        ['email' => 'belal@gmail.com'],
        ['name' => 'Belal', 'password' => bcrypt('123456'), 'role_id' => 1]
    );

    // Create a post
    $post = Post::create([
        'title' => 'Test Post for Force Delete',
        'description' => 'This post is for testing force delete.',
        'is_active' => 1,
        'user_id' => $user->id,
    ]);

    // Soft delete the post
    $response = $this->deleteJson('/api/posts/' . $post->id);
    $response->assertStatus(200);

    // Ensure the post is soft deleted
    $this->assertSoftDeleted('posts', ['id' => $post->id]);

    // Force delete the post
    $response = $this->deleteJson('/api/posts/force_delete/' . $post->id);
    $response->assertStatus(200);

    // Ensure the post is force deleted (does not exist in the database)
    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
    public function test_search_posts_successfully():void{
        $this->loginAndSetToken('belal@gmail.com', '123456'); 
        $response = $this->postJson('/api/posts/search',[
            'search'=>'po'
        ]);
        $response->assertStatus(200);     
    }
    public function test_filter_posts_successfully():void{
        $this->loginAndSetToken('belal@gmail.com', '123456'); 
        $response = $this->postJson('/api/posts/filter',[
            'from_date'=> '02-08-2024',
            'to_date'=> '02-08-2024',
        ]);
        $response->assertStatus(200);     
    }
    
}
