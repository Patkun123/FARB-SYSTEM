<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
<!-- }
    "$schema": "https://json.schemastore.org/package.json",
    "private": true,
     "type": "module",
    "scripts": {
        "build": "vite build",
         "dev": "vite"
  },
   "devDependencies": {
       "@tailwindcss/forms": "^0.5.2",
       "@tailwindcss/vite": "^4.0.0",
        "alpinejs": "^3.15.0",
       "autoprefixer": "^10.4.2",
       "axios": "^1.11.0",
       "concurrently": "^9.0.1",
       "laravel-vite-plugin": "^2.0.0",
        "postcss": "^8.4.31",
        "tailwindcss": "^3.1.0",
        "vite": "^7.0.4"
    }
}
-->

    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <!-- Alpine.js for dropdowns -->

</head>
<body class="bg-gray-100 font-sans">

<div class="flex">
    <!-- Sidebar -->
   @include('layouts.admin_navigation')

    <!-- Main Content -->
    <main class="ml-64 flex-1 p-6 overflow-y-auto">

          {{ $slot }}
    </main>
</div>

</body>
</html>

