<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found - Library Management System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f9f7f2] font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="mb-8">
                <h1 class="text-6xl font-bold text-[#2c3e50] mb-2">404</h1>
                <div class="w-16 h-1 bg-amber-600 mx-auto mb-4"></div>
                <h2 class="text-2xl font-serif font-semibold text-gray-700 mb-4">Page Not Found</h2>
                <p class="text-gray-600 max-w-md mx-auto text-lg">
                    Sorry, the page you are looking for doesn't exist or has been moved.
                </p>
            </div>
        </div>

        <!-- Illustration -->
        <div class="mb-12">
            <div class="relative">
                <!-- Stack of Books Illustration -->
                <div class="flex items-end space-x-2">
                    <div class="w-8 h-24 bg-amber-500 rounded-t-sm transform rotate-2 shadow-lg"></div>
                    <div class="w-8 h-28 bg-blue-600 rounded-t-sm transform -rotate-1 shadow-lg"></div>
                    <div class="w-8 h-32 bg-green-500 rounded-t-sm transform rotate-1 shadow-lg"></div>
                    <div class="w-8 h-26 bg-red-500 rounded-t-sm transform -rotate-2 shadow-lg"></div>
                    <div class="w-8 h-30 bg-purple-600 rounded-t-sm shadow-lg"></div>
                </div>
                
                <!-- Floating Question Mark -->
                <div class="absolute -top-8 -right-4 text-4xl text-gray-400">?</div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 items-center">
            <a href="{{ route('home') }}" 
               class="bg-amber-600 hover:bg-amber-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Back to Home
            </a>
            
            <a href="{{ route('books.index') }}" 
               class="bg-white hover:bg-gray-50 text-[#2c3e50] font-medium py-3 px-6 rounded-lg border border-gray-300 transition-colors duration-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z" />
                </svg>
                Browse Books
            </a>
        </div>

        <!-- Help Text -->
        <div class="mt-12 text-center">
            <p class="text-sm text-gray-500 mb-4">
                If you think this is an error, please contact our support team.
            </p>
            <div class="flex items-center justify-center space-x-6 text-sm text-gray-400">
                <span>Error Code: 404</span>
                <span>•</span>
                <span>{{ now()->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>
    </div>
</body>
</html>