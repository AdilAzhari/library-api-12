<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Expired - Library Management System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f9f7f2] font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="mb-8">
                <h1 class="text-6xl font-bold text-orange-600 mb-2">419</h1>
                <div class="w-16 h-1 bg-orange-600 mx-auto mb-4"></div>
                <h2 class="text-2xl font-serif font-semibold text-gray-700 mb-4">Page Expired</h2>
                <p class="text-gray-600 max-w-md mx-auto text-lg">
                    Your session has expired. Please refresh the page to continue.
                </p>
            </div>
        </div>

        <!-- Illustration -->
        <div class="mb-12">
            <div class="relative">
                <!-- Clock and Book Illustration -->
                <div class="relative bg-orange-50 rounded-lg p-8">
                    <div class="flex items-center justify-center space-x-4">
                        <!-- Book -->
                        <div class="w-12 h-16 bg-orange-300 rounded-sm shadow-lg opacity-60">
                            <div class="absolute inset-2 border border-orange-400 rounded-sm"></div>
                        </div>
                        
                        <!-- Clock -->
                        <div class="bg-white rounded-full p-4 shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                Refresh Page
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
                This usually happens when your browser has been inactive for too long.
            </p>
            <div class="flex items-center justify-center space-x-6 text-sm text-gray-400">
                <span>Error Code: 419</span>
                <span>•</span>
                <span>{{ now()->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh after 5 seconds if user doesn't take action
        setTimeout(() => {
            if (confirm('Would you like to refresh the page automatically?')) {
                window.location.reload();
            }
        }, 5000);
    </script>
</body>
</html>