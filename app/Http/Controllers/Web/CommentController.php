<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, $postId)
    {
        $request->validate([
            'author_name'  => 'required|string|max:255',
            'author_email' => 'required|email|max:255',
            'content'      => 'required|string',
        ]);

        $post = Post::findOrFail($postId);

        Comment::create([
            'post_id'      => $post->id,
            'author_name'  => $request->input('author_name'),
            'author_email' => $request->input('author_email'),
            'content'      => $request->input('content'),
            'meta_description' => substr(strip_tags($request->input('content')), 0, 150),
        ]);

        return redirect()->route('posts.show', $post->slug)->with('success', 'Your comment has been added.');
    }
}