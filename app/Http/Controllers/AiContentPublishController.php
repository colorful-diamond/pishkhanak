<?php

namespace App\Http\Controllers;

use App\Models\AiContent;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AiContentPublishController extends Controller
{
    /**
     * Publish AI content as a blog post
     */
    public function publishAsPost(Request $request, AiContent $aiContent)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published,scheduled',
            'published_at' => 'nullable|date',
        ]);

        try {
            // Generate full content from AI sections
            $content = $aiContent->generateUnifiedHtml();
            
            // Create the post with author_id
            $post = Post::create([
                'title' => $aiContent->title,
                'slug' => Str::slug($aiContent->title) . '-' . uniqid(),
                'content' => $content,
                'excerpt' => $aiContent->short_description ?? $aiContent->ai_summary,
                'category_id' => $request->category_id,
                'author_id' => Auth::id(), // Set the current user as author
                'status' => $request->status,
                'featured' => $request->featured ?? false,
                'published_at' => $request->status === 'scheduled' ? $request->published_at : now(),
                'meta_title' => $aiContent->meta_title,
                'meta_description' => $aiContent->meta_description,
                'meta_keywords' => $aiContent->meta_keywords,
                'views' => 0,
            ]);

            // Update AI content with published post reference
            $aiContent->update([
                'published_post_id' => $post->id,
                'published_at' => now(),
            ]);

            return redirect()->route('filament.admin.resources.posts.edit', $post)
                ->with('success', 'PERSIAN_TEXT_83482bbf');

        } catch (\Exception $e) {
            return back()->with('error', 'PERSIAN_TEXT_25f7b97a' . $e->getMessage());
        }
    }
}