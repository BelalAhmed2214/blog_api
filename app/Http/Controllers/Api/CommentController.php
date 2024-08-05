<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use App\Http\Requests\Api\StoreCommentRequest;
use App\Http\Requests\Api\UpdateCommentRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CommentResource;
use App\Models\Post;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use PDO;

class CommentController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    
     /**
     * @OA\Get(
     *     path="/api/comments",
     *     summary="Get all comments",
     *     tags={"comments"},
     *     @OA\Response(response="200", description="Comments retrieved successfully"),
     *     @OA\Response(response="404", description="No comments found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
     public function index()
    {
        $this->authorize('view',Comment::class);
        $comments = Comment::with('post','user')->paginate(10);
        if($comments->isEmpty()){
            return $this->returnError('Comments is Empty');
        }
        $data['comments'] = CommentResource::collection($comments);
        return $this->returnData('data',$data,'comments data');
       
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/comments",
     *     summary="Store a new comment",
     *     tags={"comments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="comment", type="string"),
     *             @OA\Property(property="post_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response="201", description="Comment created successfully"),
     *     @OA\Response(response="422", description="Validation error"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(StoreCommentRequest $request)
    {
        $this->authorize('modify',Comment::class);

        $post = Post::where('id',$request->post_id)->exists();
        $comment = new Comment();
        $comment->comment = $request->comment;
        if(!$post){
            return $this->returnError("Post Not Found");
        }
       
        $comment->post_id = $request->post_id;
        $comment->user_id = auth()->user()->id;
        if(!$comment){
            return $this->returnError("Not Found");
        }
        $comment->save();
        return $this->returnData('comment',new CommentResource($comment),'Comment has been created');
        
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/comments/{comment_id}",
     *     summary="Get a specific comment",
     *     tags={"comments"},
     *     @OA\Parameter(
     *         name="comment_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Comment retrieved successfully"),
     *     @OA\Response(response="404", description="Comment not found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show(Comment $comment)
    {
        $this->authorize('view',Comment::class);

        if(!$comment){
            return $this->returnError('Not Found');
        }
        return $this->returnData('comment',new CommentResource($comment),'Comment Data');
    }

 

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/comments/{comment_id}",
     *     summary="Update an existing comment",
     *     tags={"comments"},
     *     @OA\Parameter(
     *         name="comment_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="comment", type="string")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Comment updated successfully"),
     *     @OA\Response(response="404", description="Comment not found"),
     *     security={{"bearerAuth":{}}}
     * )
     */

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $this->authorize('modify',Comment::class);

        $comment->comment = $request->comment;
        if(!$comment){
            return $this->returnError('Not Found');
        }
        return $this->returnData('comment',new CommentResource($comment),'Comment has been updated');

    }

    /**
     * Remove the specified resource from storage.
     */

    /**
     * @OA\Delete(
     *     path="/api/comments/{comment_id}",
     *     summary="Delete a comment",
     *     tags={"comments"},
     *     @OA\Parameter(
     *         name="comment_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Comment deleted successfully"),
     *     @OA\Response(response="404", description="Comment not found"),
     *     security={{"bearerAuth":{}}}
     * )
     */

    public function destroy(Comment $comment)
    {
        $this->authorize('delete',Comment::class);

        if(!$comment){
            return $this->returnError('Not Found');
        }
        $comment->delete();
        return $this->returnData('comment',new CommentResource($comment),'Comment has been deleted');
    }
    /**
     * @OA\Get(
     *     path="/api/comments/deleted",
     *     summary="Get all deleted comments",
     *     tags={"comments"},
     *     @OA\Response(response="200", description="Deleted comments retrieved successfully"),
     *     @OA\Response(response="404", description="No deleted comments found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function deleted(){
        $this->authorize('delete',Comment::class);

        $deletedComments = Comment::with('post')->onlyTrashed()->get();
        if($deletedComments->isEmpty()){
            return $this->returnError('No Deleted Comments');
        }
        $data['deletedComments'] = CommentResource::collection($deletedComments);
        
        return $this->returnData('data',$data,'Deleted Comments');
    }
    /**
     * @OA\Post(
     *     path="/api/comments/restore/{comment_id}",
     *     summary="Restore a deleted comment",
     *     tags={"comments"},
     *     @OA\Parameter(
     *         name="comment_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Comment restored successfully"),
     *     @OA\Response(response="404", description="No deleted comment found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function restore($comment_id){
        $this->authorize('delete',Comment::class);

        $comment = Comment::with('post')->withTrashed()->where('id',$comment_id)->whereNotNull('deleted_at')->first();
        if(!$comment){
            return $this->returnError("The comment is not deleted");

        }
     
        $comment->restore();
        return $this->returnData('comment',new CommentResource($comment),'Comment Restored');
    }

    /**
     * @OA\Delete(
     *     path="/api/comments/force_delete/{comment_id}",
     *     summary="Permanently delete a comment",
     *     tags={"comments"},
     *     @OA\Parameter(
     *         name="comment_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Comment permanently deleted"),
     *     @OA\Response(response="404", description="Comment not found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function forceDelete($comment_id){
        $this->authorize('delete',Comment::class);

        $comment = Comment::with('post')->withTrashed()->where('id',$comment_id)->whereNotNull('deleted_at')->first();
        if(!$comment){
            return $this->returnError("Not Found");

        }     
        $comment->forceDelete();
        return $this->returnData('comment',new CommentResource($comment),'Comment Permanently Deleted');
    }
}
