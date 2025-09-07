<?php

namespace App\Livewire;

use App\Models\AiContent;
use App\Models\Post;
use App\Models\Category;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PublishAiContent extends Component
{
    public $aiContentId;
    public $aiContent;
    public $category_id;
    public $status = 'draft';
    public $featured = false;
    public $published_at;
    public $slug;
    
    public function mount($aiContentId)
    {
        $this->aiContentId = $aiContentId;
        $this->aiContent = AiContent::findOrFail($aiContentId);
        $this->slug = Str::slug($this->aiContent->title);
    }
    
    public function publishContent()
    {
        $this->validate([
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published,scheduled',
            'slug' => 'required|unique:posts,slug',
            'published_at' => 'nullable|date',
        ]);
        
        try {
            // Generate full content from AI sections
            $content = $this->aiContent->generateUnifiedHtml();
            
            // Create the post with the current user as author
            $post = Post::create([
                'title' => $this->aiContent->title,
                'slug' => $this->slug,
                'content' => $content,
                'excerpt' => $this->aiContent->short_description ?? $this->aiContent->ai_summary,
                'category_id' => $this->category_id,
                'author_id' => Auth::id(), // Set the current authenticated user as author
                'status' => $this->status,
                'featured' => $this->featured,
                'published_at' => $this->status === 'scheduled' ? $this->published_at : now(),
                'meta_title' => $this->aiContent->meta_title,
                'meta_description' => $this->aiContent->meta_description,
                'meta_keywords' => $this->aiContent->meta_keywords,
                'views' => 0,
            ]);
            
            // Update AI content with published post reference
            $this->aiContent->update([
                'published_post_id' => $post->id,
                'published_at' => now(),
            ]);
            
            session()->flash('success', 'PERSIAN_TEXT_83482bbf');
            
            // Redirect to the post edit page
            return redirect()->route('filament.admin.resources.posts.edit', $post);
            
        } catch (\Exception $e) {
            session()->flash('error', 'PERSIAN_TEXT_25f7b97a' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.publish-ai-content', [
            'categories' => Category::all(),
        ]);
    }
}