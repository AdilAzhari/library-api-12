<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Service Unavailable - Library Management System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f9f7f2] font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="mb-8">
                <h1 class="text-6xl font-bold text-blue-600 mb-2">503</h1>
                <div class="w-16 h-1 bg-blue-600 mx-auto mb-4"></div>
                <h2 class="text-2xl font-serif font-semibold text-gray-700 mb-4">Service Unavailable</h2>
                <p class="text-gray-600 max-w-md mx-auto text-lg">
                    We're currently performing maintenance. We'll be back shortly!
                </p>
            </div>
        </div>

        <!-- Illustration -->
        <div class="mb-12">
            <div class="relative">
                <!-- Maintenance Illustration -->
                <div class="relative bg-blue-50 rounded-lg p-8">
                    <div class="flex items-center justify-center space-x-4">
                        <!-- Books being maintained -->
                        <div class="relative">
                            <div class="w-12 h-16 bg-blue-300 rounded-sm shadow-lg">
                                <div class="absolute inset-2 border border-blue-400 rounded-sm"></div>
                            </div>
                            
                            <!-- Tools -->
                            <div class="absolute -top-2 -right-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="text-2xl">🔧</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-medium">
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>
                Maintenance in Progress
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 items-center">
            <button onclick="window.location.reload()" 
                    class="bg-amber-600 hover:bg-amber-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Check Again
            </button>
        </div>

        <!-- Information -->
        <div class="mt-12 text-center max-w-lg">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">What's happening?</h3>
            <div class="text-sm text-gray-600 space-y-2">
                <p>• System updates and improvements</p>
                <p>• Database optimization</p>
                <p>• Performance enhancements</p>
            </div>
        </div>

        <!-- Help Text -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500 mb-4">
                Estimated completion time: Usually within 30 minutes
            </p>
            <div class="flex items-center justify-center space-x-6 text-sm text-gray-400">
                <span>Error Code: 503</span>
                <span>•</span>
                <span>{{ now()->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh every 30 seconds
        setInterval(() => {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>