<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receivable Clerk</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-green-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-green-800 text-white p-6">
            <h2 class="text-xl font-bold mb-6">Receivable Clerk</h2>
            <ul class="space-y-3">
                <li><a href="{{ route('receivable.dashboard') }}" class="hover:underline">Dashboard</a></li>
                <li><a href="#" class="hover:underline">Collections</a></li>
                <li><a href="#" class="hover:underline">Reports</a></li>
            </ul>
        </aside>

        <!-- Main content -->
        <main class="flex-1 p-8">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
