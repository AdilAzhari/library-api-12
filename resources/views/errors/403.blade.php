<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Forbidden - Library Management System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f9f7f2] font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="mb-8">
                <h1 class="text-6xl font-bold text-red-600 mb-2">403</h1>
                <div class="w-16 h-1 bg-red-600 mx-auto mb-4"></div>
                <h2 class="text-2xl font-serif font-semibold text-gray-700 mb-4">Access Forbidden</h2>
                <p class="text-gray-600 max-w-md mx-auto text-lg">
                    Sorry, you don't have permission to access this resource.
                </p>
            </div>
        </div>

        <!-- Illustration -->
        <div class="mb-12">
            <div class="relative">
                <!-- Locked Book Illustration -->
                <div class="relative bg-red-100 rounded-lg p-8">
                    <div class="flex items-center justify-center">
                        <!-- Book -->
                        <div class="w-16 h-20 bg-red-600 rounded-sm shadow-lg relative">
                            <div class="absolute inset-2 border-2 border-red-300 rounded-sm"></div>
                        </div>
                        
                        <!-- Lock -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="bg-gray-800 rounded p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 items-center">
            @auth
                <a href="{{ route('dashboard') }}" 
                   class="bg-amber-600 hover:bg-amber-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 1v6m8-6v6" />
                    </svg>
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="bg-amber-600 hover:bg-amber-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Sign In
                </a>
            @endauth
            
            <a href="{{ route('home') }}" 
               class="bg-white hover:bg-gray-50 text-[#2c3e50] font-medium py-3 px-6 rounded-lg border border-gray-300 transition-colors duration-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Back to Home
            </a>
        </div>

        <!-- Help Text -->
        <div class="mt-12 text-center">
            <p class="text-sm text-gray-500 mb-4">
                If you believe you should have access to this page, please contact the administrator.
            </p>
            <div class="flex items-center justify-center space-x-6 text-sm text-gray-400">
                <span>Error Code: 403</span>
                <span>•</span>
                <span>{{ now()->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>
    </div>
</body>
</html>