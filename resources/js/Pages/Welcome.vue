<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { BookOpenIcon, UserGroupIcon, AcademicCapIcon, SparklesIcon } from '@heroicons/vue/24/outline';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: false,
    },
    phpVersion: {
        type: String,
        required: false,
    },
    totalBooks: {
        type: Number,
        default: 15000,
    },
    activeMembers: {
        type: Number,
        default: 2500,
    },
    genres: {
        type: Number,
        default: 45,
    },
    dailyLoans: {
        type: Number,
        default: 120,
    },
});
</script>

<template>
    <Head title="Welcome to BiblioTech Hub" />
    
    <!-- Main Container -->
    <div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900">
        <!-- Header -->
        <header class="relative bg-white/10 backdrop-blur-sm border-b border-white/20">
            <div class="container mx-auto px-4 py-6 flex justify-between items-center">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-amber-400 rounded-lg shadow-lg">
                        <BookOpenIcon class="h-8 w-8 text-blue-900" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-serif font-bold text-white">BiblioTech Hub</h1>
                        <p class="text-amber-300 text-sm">Your Gateway to Knowledge</p>
                    </div>
                </div>
                
                <!-- Navigation -->
                <nav v-if="canLogin" class="hidden md:flex space-x-6">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="text-white hover:text-amber-300 transition-colors font-medium"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-white hover:text-amber-300 transition-colors font-medium"
                        >
                            Sign In
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="bg-amber-500 hover:bg-amber-600 text-blue-900 font-semibold px-6 py-2 rounded-lg transition-colors shadow-lg"
                        >
                            Join Library
                        </Link>
                    </template>
                </nav>
                
                <!-- Mobile menu button -->
                <button class="md:hidden text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative py-20 text-center text-white">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto">
                    <h1 class="text-5xl md:text-6xl font-serif font-bold mb-6 leading-tight">
                        Discover Your Next <span class="text-amber-400">Literary Adventure</span>
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 opacity-90 leading-relaxed">
                        Explore thousands of books, connect with fellow readers, and embark on endless journeys through our modern digital library
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                        <Link 
                            :href="canRegister ? route('register') : route('login')" 
                            class="bg-amber-500 hover:bg-amber-600 text-blue-900 font-bold px-8 py-4 rounded-lg transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1"
                        >
                            {{ canRegister ? 'Start Your Journey' : 'Sign In to Continue' }}
                        </Link>
                        <button 
                            class="border-2 border-white text-white hover:bg-white hover:text-blue-900 font-semibold px-8 py-4 rounded-lg transition-all duration-300"
                        >
                            Explore Collection
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-16 bg-white/10 backdrop-blur-sm">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white">
                    <div class="space-y-2">
                        <div class="flex justify-center">
                            <BookOpenIcon class="h-12 w-12 text-amber-400" />
                        </div>
                        <div class="text-3xl font-bold">{{ totalBooks.toLocaleString() }}+</div>
                        <div class="text-blue-200">Books Available</div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-center">
                            <UserGroupIcon class="h-12 w-12 text-amber-400" />
                        </div>
                        <div class="text-3xl font-bold">{{ activeMembers.toLocaleString() }}+</div>
                        <div class="text-blue-200">Active Members</div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-center">
                            <AcademicCapIcon class="h-12 w-12 text-amber-400" />
                        </div>
                        <div class="text-3xl font-bold">{{ genres }}+</div>
                        <div class="text-blue-200">Genres</div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-center">
                            <SparklesIcon class="h-12 w-12 text-amber-400" />
                        </div>
                        <div class="text-3xl font-bold">{{ dailyLoans }}</div>
                        <div class="text-blue-200">Daily Loans</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-serif font-bold text-gray-900 mb-4">
                        Why Choose BiblioTech Hub?
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Experience the future of library services with our comprehensive digital platform
                    </p>
                </div>
                
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="text-center p-8 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-100 border border-blue-200">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 text-white rounded-full mb-6">
                            <BookOpenIcon class="h-8 w-8" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Vast Collection</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Access over 15,000 books across 45+ genres. From classics to contemporary works, find your next favorite read.
                        </p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="text-center p-8 rounded-xl bg-gradient-to-br from-amber-50 to-orange-100 border border-amber-200">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-600 text-white rounded-full mb-6">
                            <UserGroupIcon class="h-8 w-8" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Community Driven</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Join a vibrant community of readers. Share reviews, get recommendations, and discover new perspectives.
                        </p>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="text-center p-8 rounded-xl bg-gradient-to-br from-green-50 to-emerald-100 border border-green-200">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-600 text-white rounded-full mb-6">
                            <SparklesIcon class="h-8 w-8" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Smart Features</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Enjoy advanced search, personalized recommendations, reservation system, and seamless digital experience.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-16">
            <div class="container mx-auto px-4">
                <div class="grid md:grid-cols-4 gap-8">
                    <!-- Brand -->
                    <div class="md:col-span-2">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-amber-400 rounded-lg">
                                <BookOpenIcon class="h-6 w-6 text-gray-900" />
                            </div>
                            <div>
                                <h3 class="text-xl font-serif font-bold">BiblioTech Hub</h3>
                                <p class="text-amber-300 text-sm">Your Gateway to Knowledge</p>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6 leading-relaxed">
                            A modern digital library management system that connects readers with their next great adventure. 
                            Discover, learn, and grow with our comprehensive collection.
                        </p>
                    </div>
                    
                    <!-- Quick Links -->
                    <div>
                        <h4 class="font-semibold mb-4">Quick Links</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><Link href="/books" class="hover:text-amber-300 transition-colors">Browse Books</Link></li>
                            <li><Link href="/about" class="hover:text-amber-300 transition-colors">About Us</Link></li>
                            <li><Link href="/contact" class="hover:text-amber-300 transition-colors">Contact</Link></li>
                            <li><Link href="/terms" class="hover:text-amber-300 transition-colors">Terms of Service</Link></li>
                        </ul>
                    </div>
                    
                    <!-- Support -->
                    <div>
                        <h4 class="font-semibold mb-4">Support</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-amber-300 transition-colors">Help Center</a></li>
                            <li><a href="#" class="hover:text-amber-300 transition-colors">FAQ</a></li>
                            <li><Link href="/privacyPolicy" class="hover:text-amber-300 transition-colors">Privacy Policy</Link></li>
                            <li><a href="#" class="hover:text-amber-300 transition-colors">Library Hours</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                    <p>&copy; 2025 BiblioTech Hub. All rights reserved. | Built with ❤️ for book lovers</p>
                </div>
            </div>
        </footer>
    </div>
</template>
