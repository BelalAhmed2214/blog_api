<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePostRequest;
use App\Http\Requests\Api\UpdatePostRequest;
use App\Http\Resources\Api\PostResource;
use App\Models\Post;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use ResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Get All Posts with published users",
     *     tags={"posts"},
     *     @OA\Response(response="200", description="Posts retrieved successfully"),
     *     @OA\Response(response="404", description="No posts found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        $this->authorize('view_any', Post::class);
        //Retrieving Posts
        $posts = Post::with('user')->latest('id')->get();
        if ($posts->isEmpty()) {
            return $this->returnError('No posts found');
        }
        $data['posts'] = PostResource::collection($posts);
        return $this->returnData("data", $data, "Posts Data");
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Store Post",
     *     tags={"posts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="is_active", type="integer")
     *             )
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="is_active", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="201", description="Post created successfully"),
     *     @OA\Response(response="422", description="Validation error"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(StorePostRequest $request)
    {
        $this->authorize('modify', Post::class);

        $validated = $request->validated();
        $post = new Post();
        $post->title = $validated['title'];
        $post->description = $validated['description'];
        $post->is_active = $validated['is_active'];
        $post->user_id = auth()->user()->id;

        $post->save();
        return $this->returnData("post", new PostResource($post), "Post has been created");
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{post_id}",
     *     summary="Get a specific post",
     *     tags={"posts"},
     *     @OA\Parameter(
     *         name="post_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Post retrieved successfully"),
     *     @OA\Response(response="404", description="Post not found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show(Post $post)
    {
        $this->authorize('view_any', Post::class);
        return $this->returnData("post", new PostResource($post), "Post Data");
    }

    /**
     * @OA\Post(
     *     path="/api/posts/{post_id}",
     *     summary="Update an existing post",
     *     tags={"posts"},
     *     @OA\Parameter(
     *         name="post_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="is_active", type="integer")
     *             )
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="is_active", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Post updated successfully"),
     *     @OA\Response(response="404", description="Post not found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('modify', Post::class);

        $validated = $request->validated();
        $post->title = $validated['title'];
        $post->description = $validated['description'];
        $post->is_active = $validated['is_active'];

        $post->save();
        return $this->returnData("post", new PostResource($post), "Post has been updated");
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{post_id}",
     *     summary="Delete a post",
     *     tags={"posts"},
     *     @OA\Parameter(
     *         name="post_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Post deleted successfully"),
     *     @OA\Response(response="404", description="Post not found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', Post::class);
        $post->delete();
        return $this->returnData('post', new PostResource($post), 'Post has been deleted');
    }

    /**
     * @OA\Get(
     *     path="/api/posts/deleted",
     *     summary="Get all deleted posts",
     *     tags={"posts"},
     *     @OA\Response(response="200", description="Deleted posts retrieved successfully"),
     *     @OA\Response(response="404", description="No deleted posts found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function deleted()
    {
        $this->authorize('delete', Post::class);

        $deletedPosts = Post::onlyTrashed()->get();
        if ($deletedPosts->isEmpty()) {
            return $this->returnError('No Deleted Posts');
        }
        $data['deletedPosts'] = PostResource::collection($deletedPosts);

        return $this->returnData('data', $data, 'Deleted Posts');
    }

    /**
     * @OA\Post(
     *     path="/api/posts/restore/{post_id}",
     *     summary="Restore a deleted post",
     *     tags={"posts"},
     *     @OA\Parameter(
     *         name="post_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Post restored successfully"),
     *     @OA\Response(response="404", description="No deleted post found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function restore($post_id)
    {
        $this->authorize('delete', Post::class);

        $post = Post::withTrashed()->where('id', $post_id)->whereNotNull('deleted_at')->first();
        if (!$post) {
            return $this->returnError("This Post is not deleted");
        }

        $post->restore();
        return $this->returnData('post', new PostResource($post), 'Post Restored');
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/force_delete/{post_id}",
     *     summary="Permanently delete a post",
     *     tags={"posts"},
     *     @OA\Parameter(
     *         name="post_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Post permanently deleted"),
     *     @OA\Response(response="404", description="Post not found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function forceDelete($post_id)
    {
        $this->authorize('delete', Post::class);

        $post = Post::withTrashed()->where('id', $post_id)->whereNotNull('deleted_at')->first();
        if (!$post) {
            return $this->returnError("Not Found");
        }
        $post->forceDelete();
        return $this->returnData('post', new PostResource($post), 'Post Permanently Deleted');
    }
    /**
     * @OA\Post(
     *     path="/api/posts/search",
     *     summary="Search for a post by title",
     *     tags={"posts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="search", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Post Found"),
     *     @OA\Response(response="404", description="Post Not Found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function searchByTitle(Request $request)
    {
        $this->authorize('view_any', Post::class);

        $this->validate($request, [
            "search" => ['required', 'string'],
        ]);
        $search = $request->search;
        $results = Post::where('title', 'like', "%$search%")->get();
        if ($results->isEmpty()) {
            return $this->returnError('No data matches');
        }
        return $this->returnData('results', $results, 'Matched Data');
    }

    /**
     * @OA\Post(
     *     path="/api/posts/filter",
     *     summary="Filter and sort posts",
     *     tags={"posts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="from_date", type="string", format="date", example="28-07-2024", description="The start date for filtering posts (format: d-m-Y)"),
     *                 @OA\Property(property="to_date", type="string", format="date", example="30-07-2024", description="The end date for filtering posts (format: d-m-Y)"),
     *                 @OA\Property(property="is_active", type="integer", example=0, description="Filter posts based on their activity status"),
     *                 @OA\Property(property="sort_by", type="string", enum={"created_at", "is_active"}, example="created_at", description="The field by which to sort posts"),
     *                 @OA\Property(property="sort_direction", type="string", enum={"asc", "desc"}, example="asc", description="The direction in which to sort posts")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Post Found"),
     *     @OA\Response(response="404", description="Post Not Found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function filteringSortingPosts(Request $request)
    {

        $this->authorize('view_any', Post::class);
        //Filtering posts
        $filters = $request->validate([
            'from_date' => 'nullable|date_format:d-m-Y',
            'to_date' => 'nullable|date_format:d-m-Y',
            'is_active' => 'nullable|boolean',
            'sort_by' => 'nullable|in:created_at,is_active',
            'sort_direction' => 'nullable|in:asc,desc',
        ]);

        $is_active = (int) $request->is_active;
        if ($request->has('from_date') && $request->has('to_date')) {

            $filters['from_date'] = $request->from_date;
            $filters['to_date'] = $request->to_date;
        }
        if ($request->has('is_active')) {
            $filters['is_active'] = $is_active;
        }
        if ($request->has('sort_by')) {
            $filters['sort_by'] = $request->sort_by;
            $filters['sort_direction'] = $request->input('sort_direction');

        }

        //apply filters
        $query = Post::query();
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            // dd($filters['from_date']);

            $startDate = Carbon::createFromFormat('d-m-Y', $filters['from_date'])->startOfDay();
            $endDate = Carbon::createFromFormat('d-m-Y', $filters['to_date'])->endOfDay();
            if ($endDate < $startDate) {
                return $this->returnError("Out Of date");
            }
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        // dd($filters);
        if (isset($filters['is_active'])) {
            $query->where('is_active', '=', $filters['is_active']);
        }

        if (!empty($filters['sort_by'])) {
            $sortField = $filters['sort_by'];
            $sortDirection = $filters['sort_direction'] ?? 'asc';
            if (in_array($sortField, ['created_at', 'is_active']) && in_array($sortDirection, ['asc', 'desc'])) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('id', 'asc');
            }
        }

        //Retrieving Posts
        $posts = $query->get();
        $data['posts'] = PostResource::collection($posts);
        return $this->returnData("data", $data, "Posts Data");
    }

}
