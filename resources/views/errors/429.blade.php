<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Too Many Requests - Library Management System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f9f7f2] font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="mb-8">
                <h1 class="text-6xl font-bold text-yellow-600 mb-2">429</h1>
                <div class="w-16 h-1 bg-yellow-600 mx-auto mb-4"></div>
                <h2 class="text-2xl font-serif font-semibold text-gray-700 mb-4">Too Many Requests</h2>
                <p class="text-gray-600 max-w-md mx-auto text-lg">
                    Slow down! You've made too many requests. Please wait a moment before trying again.
                </p>
            </div>
        </div>

        <!-- Illustration -->
        <div class="mb-12">
            <div class="relative">
                <!-- Speed Meter Illustration -->
                <div class="relative bg-yellow-50 rounded-lg p-8">
                    <div class="flex items-center justify-center">
                        <!-- Speed gauge -->
                        <div class="relative">
                            <div class="w-24 h-12 border-8 border-yellow-300 border-b-transparent rounded-t-full"></div>
                            <div class="absolute top-8 left-1/2 transform -translate-x-1/2 w-1 h-8 bg-red-500 origin-bottom rotate-45"></div>
                            <div class="absolute top-10 left-1/2 transform -translate-x-1/2 w-3 h-3 bg-yellow-600 rounded-full"></div>
                        </div>
                    </div>
                    <div class="text-center mt-4 text-yellow-700 font-medium">Rate Limited</div>
                </div>
            </div>
        </div>

        <!-- Wait Timer -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center gap-2 bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Please wait <span id="countdown">60</span> seconds
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 items-center">            
            <a href="{{ route('home') }}" 
               class="bg-white hover:bg-gray-50 text-[#2c3e50] font-medium py-3 px-6 rounded-lg border border-gray-300 transition-colors duration-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Back to Home
            </a>
        </div>

        <!-- Information -->
        <div class="mt-12 text-center max-w-lg">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Why did this happen?</h3>
            <div class="text-sm text-gray-600 space-y-2">
                <p>• You've made too many requests in a short time</p>
                <p>• Our servers are protecting against overload</p>
                <p>• This helps ensure good performance for everyone</p>
            </div>
        </div>

        <!-- Help Text -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500 mb-4">
                The rate limit will reset automatically in a few moments.
            </p>
            <div class="flex items-center justify-center space-x-6 text-sm text-gray-400">
                <span>Error Code: 429</span>
                <span>•</span>
                <span>{{ now()->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>
    </div>

    <script>
        let countdown = 60;
        const countdownElement = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timer);
                location.reload();
            }
        }, 1000);
    </script>
</body>
</html>