<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-vertical-style="overlay" data-toggled="icon-overlay-close" class="light" data-header-styles="light" data-menu-styles="dark">
  <head>
    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="Description" content="Laravel Tailwind CSS Responsive Admin Web Dashboard Template">
    <meta name="keywords" content="admin panel in laravel, tailwind, tailwind template admin, laravel admin panel, tailwind css dashboard, admin dashboard template, admin template, tailwind laravel, template dashboard, admin panel tailwind, tailwind css admin template, laravel tailwind template, laravel tailwind, tailwind admin dashboard">
    
    <!-- FAVICON -->
    <link rel="icon" href="{{ asset('build/assets/images/brand-logos/favicon.ico') }}" type="image/x-icon">

    <!-- MAIN STYLES (Static) -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    
    <!-- ICONS CSS (Static) -->
    <link href="{{ asset('assets/iconfonts/icons.css') }}" rel="stylesheet">

    <!-- Vite Assets -->
    @vite([
      'resources/sass/app.scss',
      'resources/assets/css/style.css',
      'resources/assets/iconfonts/icons.css',
      'resources/js/app.js'
    ])
    
    @inertiaHead
  </head>
  <body>
    @inertia
    
    <!-- Responsive overlay for mobile menu -->
    <div id="responsive-overlay"></div>
  </body>
</html>