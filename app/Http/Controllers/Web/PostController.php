<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use SEO;

class PostController extends Controller
{
    /**
     * Display a listing of the posts.
     */
    public function index()
    {
        $posts = Post::with('category', 'tags')->paginate(10);

        // Set SEO meta tags
        SEO::setTitle('Blog Posts');
        SEO::setDescription('Browse our latest blog posts on various topics.');
        SEO::setCanonical(url()->current());
        SEO::opengraph()->setUrl(url()->current());
        SEO::opengraph()->addProperty('type', 'website');
        SEO::twitter()->setSite('@' . config('app.twitter_handle'));

        return view('front.posts.index', compact('posts'));
    }

    /**
     * Display the specified post.
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)->with('category', 'tags', 'comments')->firstOrFail();

        // Set SEO meta tags
        SEO::setTitle($post->meta_title ?? $post->title);
        SEO::setDescription($post->meta_description ?? substr(strip_tags($post->content), 0, 150));
        SEO::setKeywords($post->meta_keywords);
        SEO::opengraph()->setUrl(url()->current());
        SEO::setCanonical(url()->current());
        SEO::opengraph()->addProperty('type', 'article');
        SEO::opengraph()->setTitle($post->og_title ?? $post->title);
        SEO::opengraph()->setDescription($post->og_description ?? substr(strip_tags($post->content), 0, 150));
        if ($post->og_image) {
            SEO::addImages(asset($post->og_image));
        }
        SEO::twitter()->setSite('@' . config('app.twitter_handle'));
        SEO::twitter()->setTitle($post->twitter_title ?? $post->title);
        SEO::twitter()->setDescription($post->twitter_description ?? substr(strip_tags($post->content), 0, 150));
        if ($post->twitter_image) {
            SEO::twitter()->setImage(asset($post->twitter_image));
        }
        SEO::jsonLd()->setType('Article');
        SEO::jsonLd()->setTitle($post->title);
        SEO::jsonLd()->setDescription(substr(strip_tags($post->content), 0, 150));
        if ($post->og_image) {
            SEO::jsonLd()->addImage(asset($post->og_image));
        }

        return view('front.posts.show', compact('post'));
    }
}