<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Error - Library Management System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f9f7f2] font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="mb-8">
                <h1 class="text-6xl font-bold text-red-600 mb-2">500</h1>
                <div class="w-16 h-1 bg-red-600 mx-auto mb-4"></div>
                <h2 class="text-2xl font-serif font-semibold text-gray-700 mb-4">Server Error</h2>
                <p class="text-gray-600 max-w-md mx-auto text-lg">
                    Something went wrong on our end. We're working to fix it!
                </p>
            </div>
        </div>

        <!-- Illustration -->
        <div class="mb-12">
            <div class="relative">
                <!-- Broken Book Stack Illustration -->
                <div class="relative">
                    <div class="flex items-end space-x-2">
                        <div class="w-8 h-24 bg-gray-400 rounded-t-sm transform rotate-12 shadow-lg opacity-60"></div>
                        <div class="w-8 h-28 bg-gray-500 rounded-t-sm transform -rotate-6 shadow-lg opacity-80"></div>
                        <div class="w-8 h-20 bg-red-500 rounded-t-sm transform rotate-45 shadow-lg"></div>
                        <div class="w-8 h-16 bg-gray-600 rounded-t-sm transform -rotate-12 shadow-lg opacity-60"></div>
                    </div>
                    
                    <!-- Error Symbol -->
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <div class="bg-red-100 rounded-full p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 items-center">
            <button onclick="window.location.reload()" 
                    class="bg-amber-600 hover:bg-amber-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Try Again
            </button>
            
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
                If this problem persists, please report it to our technical team.
            </p>
            <div class="flex items-center justify-center space-x-6 text-sm text-gray-400">
                <span>Error Code: 500</span>
                <span>•</span>
                <span>{{ now()->format('Y-m-d H:i:s') }}</span>
                @if(config('app.debug'))
                    <span>•</span>
                    <span>Debug Mode</span>
                @endif
            </div>
        </div>

        <!-- Debug Information (only in debug mode) -->
        @if(config('app.debug') && isset($exception))
            <div class="mt-8 max-w-4xl w-full mx-auto">
                <details class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <summary class="cursor-pointer text-red-700 font-medium mb-2">Debug Information</summary>
                    <div class="text-sm text-red-600 font-mono">
                        <p class="mb-2"><strong>File:</strong> {{ $exception->getFile() ?? 'Unknown' }}</p>
                        <p class="mb-2"><strong>Line:</strong> {{ $exception->getLine() ?? 'Unknown' }}</p>
                        <p class="mb-4"><strong>Message:</strong> {{ $exception->getMessage() ?? 'No message available' }}</p>
                    </div>
                </details>
            </div>
        @endif
    </div>
</body>
</html>