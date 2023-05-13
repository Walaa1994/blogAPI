<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use App\Http\Resources\Post as PostResource;

class PostController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();
        return $this->sendResponse(PostResource::collection($posts), 'All posts retrieved successfully');
    }

    public function userPosts($id)
    {
        $posts = Post::where('user_id',$id)->get();
        return $this->sendResponse(PostResource::collection($posts), 'User posts retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input,[
            'title' => 'required',
            'content' => 'required',
         ]);

        if($validator->fails()){
            return $this->sendError('Please validate the error',$validator->errors());
        }

        $input['user_id'] = Auth::user()->id;
        $post = Post::create($input);
        return $this->sendResponse(new PostResource($post),'Post created successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return $this->sendResponse(new PostResource($post),'Post retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $input = $request->all();
        $validator = Validator::make($input,[
            'title' => 'required',
            'content' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Please validate the error',$validator->errors());
        }

        if($post->user_id != Auth::id()){
            return $this->sendError('Sorry, you can not update the post');
        }

        $post->title = $input['title'];
        $post->content = $input['content'];
        $post->save();

        return $this->sendResponse(new PostResource($post),'Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if($post->user_id != Auth::id()){
            return $this->sendError('Sorry, you can not update the post');
        }

        $post->delete();
        return $this->sendResponse(new PostResource($post),'Post deleted successfully');

    }
}
