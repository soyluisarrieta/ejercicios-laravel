<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PostController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $posts = Post::all();
    return PostResource::collection($posts);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $post = Post::create($request->all());
    return response()->json($post);
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $post = Post::find($id);
    return new PostResource($post);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $post)
  {
    try {
      $post = Post::findOrFail($post);
      $post->update($request->all());
      return response()->json(['success' => true, 'message' => 'Post updated successfully', 'data' => $post]);
    } catch (ModelNotFoundException $e) {
      return response()->json(['success' => false, 'message' => 'Post not found'], 404);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => 'An error occurred while updating the post'], 500);
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Post $post)
  {
    $post->delete();
    return response()->json(['success' => true, 'message' => 'Post deleted'], 204);
  }
}
