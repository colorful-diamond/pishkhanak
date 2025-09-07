<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use SEO;

class CategoryController extends Controller
{
    /**
     * Display a listing of posts under a specific category.
     */
    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $posts = $category->posts()->with('tags')->paginate(10);

        // Set SEO meta tags
        SEO::setTitle($category->meta_title ?? $category->name);
        SEO::setDescription($category->meta_description ?? 'All posts under ' . $category->name);
        SEO::setKeywords($category->meta_keywords);
        SEO::opengraph()->setUrl(url()->current());
        SEO::setCanonical(url()->current());
        SEO::opengraph()->addProperty('type', 'website');
        SEO::opengraph()->setTitle($category->og_title ?? $category->name);
        SEO::opengraph()->setDescription($category->og_description ?? 'All posts under ' . $category->name);
        if ($category->og_image) {
            SEO::addImages(asset($category->og_image));
        }
        SEO::twitter()->setSite('@' . config('app.twitter_handle'));
        SEO::twitter()->setTitle($category->twitter_title ?? $category->name);
        SEO::twitter()->setDescription($category->twitter_description ?? 'All posts under ' . $category->name);
        if ($category->twitter_image) {
            SEO::twitter()->setImage(asset($category->twitter_image));
        }
        SEO::jsonLd()->setType('WebPage');
        SEO::jsonLd()->setTitle($category->name);
        SEO::jsonLd()->setDescription('All posts under ' . $category->name);
        if ($category->og_image) {
            SEO::jsonLd()->addImage(asset($category->og_image));
        }

        return view('front.categories.show', compact('category', 'posts'));
    }
}