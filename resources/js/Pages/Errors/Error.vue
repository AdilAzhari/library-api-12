<template>
    <div class="min-h-screen bg-[#f9f7f2] font-sans antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center px-4">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="mb-8">
                    <h1 :class="getStatusClass()" class="text-6xl font-bold mb-2">{{ status }}</h1>
                    <div :class="getBarClass()" class="w-16 h-1 mx-auto mb-4"></div>
                    <h2 class="text-2xl font-serif font-semibold text-gray-700 mb-4">{{ getErrorTitle() }}</h2>
                    <p class="text-gray-600 max-w-md mx-auto text-lg">
                        {{ getErrorMessage() }}
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
                    
                    <!-- Dynamic Icon based on error type -->
                    <div class="absolute -top-8 -right-4 text-4xl">
                        <span v-if="status == 404" class="text-gray-400">?</span>
                        <span v-else-if="status == 403" class="text-red-500">🔒</span>
                        <span v-else-if="status == 500" class="text-red-500">⚠️</span>
                        <span v-else-if="status == 503" class="text-blue-500">🔧</span>
                        <span v-else-if="status == 419" class="text-orange-500">⏰</span>
                        <span v-else-if="status == 429" class="text-yellow-500">⚡</span>
                        <span v-else class="text-gray-400">❌</span>
                    </div>
                </div>
            </div>

            <!-- Status Indicator (for specific errors) -->
            <div v-if="showStatusIndicator()" class="mb-8 text-center">
                <div :class="getStatusIndicatorClass()" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium">
                    <div v-if="status == 503" class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>
                    <div v-else-if="status == 429" class="w-2 h-2 bg-yellow-600 rounded-full animate-pulse"></div>
                    {{ getStatusText() }}
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 items-center">
                <!-- Primary Action -->
                <button v-if="showPrimaryAction()" 
                        @click="handlePrimaryAction()"
                        class="bg-amber-600 hover:bg-amber-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    {{ getPrimaryActionText() }}
                </button>

                <!-- Secondary Actions -->
                <Link v-if="status == 404"
                      :href="route('books.index')"
                      class="bg-amber-600 hover:bg-amber-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z" />
                    </svg>
                    Browse Books
                </Link>

                <Link v-if="status == 403 && $page.props.auth?.user"
                      :href="route('dashboard')"
                      class="bg-amber-600 hover:bg-amber-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 1v6m8-6v6" />
                    </svg>
                    Go to Dashboard
                </Link>

                <Link v-if="status == 403 && !$page.props.auth?.user"
                      :href="route('login')"
                      class="bg-amber-600 hover:bg-amber-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Sign In
                </Link>
                
                <Link :href="route('home')"
                      class="bg-white hover:bg-gray-50 text-[#2c3e50] font-medium py-3 px-6 rounded-lg border border-gray-300 transition-colors duration-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Back to Home
                </Link>
            </div>

            <!-- Information Section -->
            <div v-if="showInformation()" class="mt-12 text-center max-w-lg">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ getInformationTitle() }}</h3>
                <div class="text-sm text-gray-600 space-y-2">
                    <p v-for="info in getInformationItems()" :key="info">{{ info }}</p>
                </div>
            </div>

            <!-- Help Text -->
            <div class="mt-12 text-center">
                <p class="text-sm text-gray-500 mb-4">
                    {{ getHelpText() }}
                </p>
                <div class="flex items-center justify-center space-x-6 text-sm text-gray-400">
                    <span>Error Code: {{ status }}</span>
                    <span>•</span>
                    <span>{{ currentTime }}</span>
                    <span v-if="$page.props.app?.debug">•</span>
                    <span v-if="$page.props.app?.debug">Debug Mode</span>
                </div>
            </div>

            <!-- Debug Information (only in debug mode) -->
            <div v-if="$page.props.app?.debug && exception" class="mt-8 max-w-4xl w-full mx-auto">
                <details class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <summary class="cursor-pointer text-red-700 font-medium mb-2">Debug Information</summary>
                    <div class="text-sm text-red-600 font-mono">
                        <p class="mb-2"><strong>File:</strong> {{ exception.file || 'Unknown' }}</p>
                        <p class="mb-2"><strong>Line:</strong> {{ exception.line || 'Unknown' }}</p>
                        <p class="mb-4"><strong>Message:</strong> {{ exception.message || 'No message available' }}</p>
                    </div>
                </details>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    status: {
        type: [String, Number],
        required: true
    },
    exception: {
        type: Object,
        default: null
    }
})

const currentTime = ref('')

onMounted(() => {
    currentTime.value = new Date().toLocaleString()
    
    // Auto-refresh for certain errors
    if (props.status == 419) {
        setTimeout(() => {
            if (confirm('Would you like to refresh the page automatically?')) {
                window.location.reload()
            }
        }, 5000)
    }
    
    if (props.status == 503) {
        setInterval(() => {
            window.location.reload()
        }, 30000)
    }
})

const getStatusClass = () => {
    switch (props.status.toString()) {
        case '404': return 'text-amber-600'
        case '403': return 'text-red-600'
        case '500': return 'text-red-600'
        case '503': return 'text-blue-600'
        case '419': return 'text-orange-600'
        case '429': return 'text-yellow-600'
        default: return 'text-gray-600'
    }
}

const getBarClass = () => {
    switch (props.status.toString()) {
        case '404': return 'bg-amber-600'
        case '403': return 'bg-red-600'
        case '500': return 'bg-red-600'
        case '503': return 'bg-blue-600'
        case '419': return 'bg-orange-600'
        case '429': return 'bg-yellow-600'
        default: return 'bg-gray-600'
    }
}

const getErrorTitle = () => {
    switch (props.status.toString()) {
        case '404': return 'Page Not Found'
        case '403': return 'Access Forbidden'
        case '500': return 'Server Error'
        case '503': return 'Service Unavailable'
        case '419': return 'Page Expired'
        case '429': return 'Too Many Requests'
        default: return 'Error'
    }
}

const getErrorMessage = () => {
    switch (props.status.toString()) {
        case '404': return "Sorry, the page you are looking for doesn't exist or has been moved."
        case '403': return "Sorry, you don't have permission to access this resource."
        case '500': return "Something went wrong on our end. We're working to fix it!"
        case '503': return "We're currently performing maintenance. We'll be back shortly!"
        case '419': return "Your session has expired. Please refresh the page to continue."
        case '429': return "Slow down! You've made too many requests. Please wait a moment before trying again."
        default: return "An error occurred while processing your request."
    }
}

const showStatusIndicator = () => {
    return ['503', '429'].includes(props.status.toString())
}

const getStatusIndicatorClass = () => {
    switch (props.status.toString()) {
        case '503': return 'bg-blue-100 text-blue-800'
        case '429': return 'bg-yellow-100 text-yellow-800'
        default: return ''
    }
}

const getStatusText = () => {
    switch (props.status.toString()) {
        case '503': return 'Maintenance in Progress'
        case '429': return 'Rate Limited'
        default: return ''
    }
}

const showPrimaryAction = () => {
    return ['500', '419', '503'].includes(props.status.toString())
}

const getPrimaryActionText = () => {
    switch (props.status.toString()) {
        case '500': return 'Try Again'
        case '419': return 'Refresh Page'
        case '503': return 'Check Again'
        default: return 'Refresh'
    }
}

const handlePrimaryAction = () => {
    window.location.reload()
}

const showInformation = () => {
    return ['503', '429'].includes(props.status.toString())
}

const getInformationTitle = () => {
    switch (props.status.toString()) {
        case '503': return "What's happening?"
        case '429': return "Why did this happen?"
        default: return ''
    }
}

const getInformationItems = () => {
    switch (props.status.toString()) {
        case '503': 
            return [
                '• System updates and improvements',
                '• Database optimization',
                '• Performance enhancements'
            ]
        case '429':
            return [
                "• You've made too many requests in a short time",
                '• Our servers are protecting against overload',
                '• This helps ensure good performance for everyone'
            ]
        default: return []
    }
}

const getHelpText = () => {
    switch (props.status.toString()) {
        case '404': return "If you think this is an error, please contact our support team."
        case '403': return "If you believe you should have access to this page, please contact the administrator."
        case '500': return "If this problem persists, please report it to our technical team."
        case '503': return "Estimated completion time: Usually within 30 minutes"
        case '419': return "This usually happens when your browser has been inactive for too long."
        case '429': return "The rate limit will reset automatically in a few moments."
        default: return "If you need assistance, please contact our support team."
    }
}
</script>