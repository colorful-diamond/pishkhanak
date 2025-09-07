<html lang="fa-IR" dir="rtl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {!! SEO::generate(true) !!}
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('styles')
</head>

<body dir="rtl">
  <div class="flex overflow-hidden flex-col gap-5 items-center rounded-3xl bg-gradient-to-br from-sky-50 to-indigo-50 max-md:grow-0 max-sm:text-right">
    
    @include('front.partials.header')
    @include('front.partials.header-nav')
    <main class="flex flex-col min-h-screen/2/2 self-center w-full rounded-[32px] max-md:max-w-full max-sm:px-2.5">
      <div class="box-border flex relative flex-col shrink-0 mx-auto w-full max-w-screen-lg">
        @yield('content')
      </div>
    </main>
    <section class="modals fixed inset-0 flex items-center justify-center z-50 hidden transition-transform duration-500 ease-in-out transform hover:scale-105">
      <div class="p-0 max-w-[768px] w-full">
        @include('front.partials.modals.comment')
      </div>
    </section>
    @include('front.partials.footer-dynamic')
    @include('front.partials.footer-nav')
    @include('front.partials.sidebar2')
  </div>
  <div id="overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-30 transition-opacity duration-300 ease-in-out"></div>
  
  <!-- Enhanced Sidebars Script -->
  <script src="{{ asset('js/enhanced-sidebars.js') }}"></script>
  
  @stack('scripts')

  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=AW-11493904451">
  </script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'AW-11493904451');
  </script>
</body>
</html>