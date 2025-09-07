<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Artesaos\SEOTools\Facades\SEO;
use App\Models\Post;
use App\Models\Category;
use App\Traits\SeoTrait;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    use SeoTrait;
    
    public function index(Request $request)
    {
        $this->setSeo([
            'title' => 'وبلاگ - آخرین اخبار و مقالات',
            'description' => 'آخرین اخبار و مقالات در مورد استعلامات، خدمات بانکی، و اطلاعات مفید',
            'keywords' => ['وبلاگ', 'استعلام آنلاین', 'خدمات بانکی', 'اخبار', 'مقالات', 'پیشخوانک'],
            'type' => 'website',
            'jsonld_type' => 'Blog',
            'breadcrumbs' => [
                ['name' => 'خانه', 'url' => route('app.page.home')],
                ['name' => 'وبلاگ']
            ],
            'image' => asset('assets/logo-lg.png')
        ]);

        $categories = Category::all();
        $posts = Post::published()->latest()->paginate(10);
        return view('front.blog.index', compact('posts', 'categories'));
    }

    public function show($slug)
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();
        
        // Set SEO for blog post
        $this->setBlogSeo($post);
        
        return view('front.blog.single', compact('post'));
    }
}