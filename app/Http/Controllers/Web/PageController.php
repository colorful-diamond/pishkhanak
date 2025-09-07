<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;
use App\Traits\SeoTrait;

class PageController extends Controller
{
    use SeoTrait;
    /**
     * Display the specified page.
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        $title = $page->meta_title ?? $page->title;
        $description = $page->meta_description ?? substr(strip_tags($page->content), 0, 150);
        $keywords = $page->meta_keywords;
        $url = url()->current();

        // Set SEOMeta
        SEOMeta::setTitle($title)
            ->setDescription($description)
            ->setKeywords($keywords)
            ->setCanonical($url);

        // Set OpenGraph
        OpenGraph::setTitle($page->og_title ?? $page->title)
            ->setDescription($page->og_description ?? $description)
            ->setUrl($url)
            ->addProperty('type', 'article');

        if ($page->og_image) {
            OpenGraph::addImage(asset($page->og_image));
        }

        // Set TwitterCard
        TwitterCard::setSite('@' . config('app.twitter_handle'))
            ->setTitle($page->twitter_title ?? $page->title)
            ->setDescription($page->twitter_description ?? $description);

        if ($page->twitter_image) {
            TwitterCard::setImage(asset($page->twitter_image));
        }

        // Set JsonLd
        JsonLd::setType('WebPage')
            ->setTitle($page->title)
            ->setDescription($description)
            ->setUrl($url);

        if ($page->og_image) {
            JsonLd::setImage(asset($page->og_image));
        }

        return view('front.pages.show', compact('page'));
    }

    //Custome Pages Functions

    public function showHome()
    {
        // Set SEO for homepage
        $this->setSeo([
            'title' => 'استعلام هر آنچه که می خواهید!',
            'description' => 'پیشخوانک، مرجع جامع خدمات استعلام آنلاین شامل استعلام بانکی، خودرو، مالیاتی، جواز کسب و سایر خدمات ضروری. آسان، سریع و معتبر.',
            'keywords' => ['استعلام آنلاین', 'کارت به شبا', 'استعلام خودرو', 'خلافی خودرو', 'استعلام مالیاتی', 'جواز کسب', 'پیشخوانک'],
            'type' => 'website',
            'jsonld_type' => 'WebSite',
            'image' => asset('assets/logo-lg.png')
        ]);

        // Add website schema
        JsonLd::addValue('potentialAction', [
            '@type' => 'SearchAction',
            'target' => route('app.page.home') . '?q={search_term_string}',
            'query-input' => 'required name=search_term_string'
        ]);

        // Get categories with their services (only parent services)
        $categories = ServiceCategory::with(['services' => function($query) {
                $query->where('status', 'active')
                      ->whereNull('parent_id')
                      ->orderBy('created_at', 'desc');
            }])
            ->get();

        // Get popular services for quick links
        $popularServices = Service::where('status', 'active')
            ->where('featured', true)
            ->orderBy('views', 'desc')
            ->limit(3)
            ->get();

        // Get active services for homepage sections
        $bankingServices = Service::where('status', 'active')
            ->whereHas('category', function($query) {
                $query->where('name', 'like', '%بانک%')
                      ->orWhere('name', 'like', '%مالی%')
                      ->orWhere('name', 'like', '%پرداخت%');
            })
            ->orderBy('views', 'desc')
            ->limit(6)
            ->get();

        $vehicleServices = Service::where('status', 'active')
            ->whereHas('category', function($query) {
                $query->where('name', 'like', '%خودرو%')
                      ->orWhere('name', 'like', '%موتور%')
                      ->orWhere('name', 'like', '%ترافیک%');
            })
            ->orderBy('views', 'desc')
            ->limit(4)
            ->get();

        $otherServices = Service::where('status', 'active')
            ->whereDoesntHave('category', function($query) {
                $query->where('name', 'like', '%بانک%')
                      ->orWhere('name', 'like', '%مالی%')
                      ->orWhere('name', 'like', '%پرداخت%')
                      ->orWhere('name', 'like', '%خودرو%')
                      ->orWhere('name', 'like', '%موتور%')
                      ->orWhere('name', 'like', '%ترافیک%');
            })
            ->orderBy('views', 'desc')
            ->limit(4)
            ->get();

        return view('front.pages.custom.home', compact(
            'categories',
            'popularServices',
            'bankingServices',
            'vehicleServices',
            'otherServices'
        ));
    }

    public function showAbout()
    {
        $this->setSeo([
            'title' => 'درباره ما',
            'description' => 'آشنایی با پیشخوانک، مرجع معتبر ارائه خدمات استعلام آنلاین. تاریخچه، اهداف و تیم پیشخوانک.',
            'keywords' => ['درباره ما', 'پیشخوانک', 'خدمات استعلام', 'تیم پیشخوانک'],
            'breadcrumbs' => [
                ['name' => 'خانه', 'url' => route('app.page.home')],
                ['name' => 'درباره ما']
            ]
        ]);

        return view('front.pages.custom.about');
    }

    public function showServices()
    {
        return view('front.pages.custom.services');
    }

    public function showContact()
    {
        $this->setSeo([
            'title' => 'تماس با ما',
            'description' => 'برای ارتباط با تیم پشتیبانی پیشخوانک، ارسال پیشنهادات و انتقادات از این صفحه استفاده کنید.',
            'keywords' => ['تماس با ما', 'پشتیبانی', 'پیشخوانک', 'ارتباط'],
            'breadcrumbs' => [
                ['name' => 'خانه', 'url' => route('app.page.home')],
                ['name' => 'تماس با ما']
            ]
        ]);

        // Add contact schema
        JsonLd::addValue('contactPoint', [
            '@type' => 'ContactPoint',
            'contactType' => 'customer service',
            'email' => 'info@pishkhanak.com',
            'availableLanguage' => 'Persian'
        ]);

        $captchaNumber = mt_rand(10000, 99999);
        Session::put('captcha_value', (string)$captchaNumber);

        return view('front.pages.custom.contact');
    }

    public function showPrivacyPolicy()
    {
        $this->setSeo([
            'title' => 'سیاست حفظ حریم خصوصی',
            'description' => 'سیاست حفظ حریم خصوصی و حفاظت از اطلاعات شخصی کاربران در پلتفرم پیشخوانک',
            'keywords' => ['حریم خصوصی', 'حفاظت داده', 'قوانین حریم خصوصی', 'پیشخوانک'],
            'breadcrumbs' => [
                ['name' => 'خانه', 'url' => route('app.page.home')],
                ['name' => 'حریم خصوصی']
            ]
        ]);

        return view('front.pages.custom.privacy-policy');
    }

    public function showTermsConditions()
    {
        $this->setSeo([
            'title' => 'شرایط و قوانین استفاده',
            'description' => 'شرایط و قوانین استفاده از خدمات پلتفرم پیشخوانک',
            'keywords' => ['شرایط استفاده', 'قوانین', 'مقررات', 'پیشخوانک'],
            'breadcrumbs' => [
                ['name' => 'خانه', 'url' => route('app.page.home')],
                ['name' => 'شرایط و قوانین']
            ]
        ]);

        return view('front.pages.custom.terms-conditions');
    }

    public function generateCaptchaImage()
    {
        try {
            $manager = new ImageManager(new Driver());
            $captchaText = Session::get('captcha_value', '-----');

            $image = $manager->create(150, 50)->fill('#f0f0f0');
            $fontPath = public_path('assets/fonts/IRANSansWeb_Bold.ttf');

            $image->text($captchaText, 75, 25, function($font) use ($fontPath) {
                $font->file($fontPath);
                $font->size(28);
                $font->color('#333333');
                $font->align('center');
                $font->valign('middle');
            });

            for ($i = 0; $i < 3; $i++) {
                $image->drawLine(function ($line) {
                    $line->from(mt_rand(0, 150), mt_rand(0, 50));
                    $line->to(mt_rand(0, 150), mt_rand(0, 50));
                    $line->color(sprintf('#%02x%02x%02x', mt_rand(100, 200), mt_rand(100, 200), mt_rand(100, 200)));
                    $line->width(1);
                });
            }

            $encodedImage = $image->toPng();

            $response = Response::make($encodedImage);
            $response->header('Content-Type', 'image/png');
            $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->header('Pragma', 'no-cache');
            $response->header('Expires', '0');

            return $response;

        } catch (\Exception $e) {
            Log::error('Error generating CAPTCHA image: ' . $e->getMessage());
            return Response::make('Error generating image', 500);
        }
    }

    public function showLogin()
    {
        return redirect()->route('app.auth.login');
    }

    public function logout()
    {
        return redirect()->route('app.auth.logout');
    }

    public function show404()
    {
        return view('front.pages.custom.404');
    }
}