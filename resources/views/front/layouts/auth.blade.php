<html lang="fa-IR" dir="rtl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {!! SEO::generate(true) !!}
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('styles')
</head>

<body dir="rtl" class="font-sans antialiased">
  <div class="min-h-screen/2/2 flex flex-col">
    
    <main class="flex-1">
      @yield('content')
    </main>
    
  </div>
  
  @stack('scripts')
</body>
</html> 