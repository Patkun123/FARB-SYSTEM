<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

       

        <style>
            /* Metallic text effect */
            .metallic-text {
                background: linear-gradient(135deg, #e5e5e5, #cfcfcf, #9e9e9e, #f5f5f5);
                background-size: 300% 300%;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                animation: shine 6s infinite linear;
            }
            @keyframes shine {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            /* Metallic buttons */
            .metallic-btn {
                background: linear-gradient(145deg, #e0e0e0, #b0b0b0);
                color: #1f1f1f;
                border: none;
                box-shadow: 0 3px 8px rgba(0,0,0,0.3);
                transition: all 0.3s ease-in-out;
            }
            .metallic-btn:hover {
                background: linear-gradient(145deg, #ffffff, #b8b8b8);
                transform: translateY(-2px);
                box-shadow: 0 6px 12px rgba(0,0,0,0.4);
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-slate-800 via-slate-900 to-black text-gray-200">

        <div class="min-h-screen flex flex-col sm:justify-center items-center p-6">
            <!-- Logo -->
            <div class="mb-6">
                <a href="/" class="flex flex-col items-center">
                    <x-application-logo class="w-20 h-20 logo-glow" />
                    <h1 class="mt-4 text-2xl font-bold metallic-text">FARB SYSTEM</h1>
                </a>
            </div>

            <!-- Card -->
            <div class="w-full sm:max-w-md px-8 py-6 bg-white/10 backdrop-blur-lg border border-white/20 shadow-2xl rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
