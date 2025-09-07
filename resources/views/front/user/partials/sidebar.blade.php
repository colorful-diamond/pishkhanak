@props(['currentRoute' => request()->route()->getName()])

<!-- Mobile Sidebar (visible on mobile only) -->
<div class="lg:hidden col-span-4">
    @include('front.user.partials.mobile-sidebar', ['currentRoute' => $currentRoute])
</div>

<!-- Desktop Sidebar (hidden on mobile) -->
<div class="hidden lg:block lg:col-span-1">
    @include('front.user.partials.desktop-sidebar', ['currentRoute' => $currentRoute])
</div> 