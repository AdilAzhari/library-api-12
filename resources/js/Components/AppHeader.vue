<!--<template>-->
<!--    <header class="bg-[#2c3e50] text-white shadow-lg sticky top-0 z-50">-->
<!--        <div class="container mx-auto px-4 py-4 flex justify-between items-center">-->
<!--            <div class="flex items-center space-x-3">-->
<!--                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-400" fill="none" viewBox="0 0 24 24"-->
<!--                     stroke="currentColor">-->
<!--                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"-->
<!--                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>-->
<!--                </svg>-->
<!--                <h1 class="text-xl font-serif font-bold">Academia Inspired</h1>-->
<!--            </div>-->
<!--            <nav class="hidden md:flex space-x-6 items-center">-->
<!--                <Link href="/books" class="hover:text-amber-300 transition">Browse</Link>-->
<!--                <Link href="/borrows" class="hover:text-amber-300 transition">My Loans</Link>-->
<!--                <Link href="/reservations" class="hover:text-amber-300 transition">Reservations</Link>-->
<!--                <Link href="/books/create" class="bg-amber-500 hover:bg-amber-600 px-4 py-2 rounded transition">Add-->
<!--                    Book-->
<!--                </Link>-->
<!--            </nav>-->
<!--            <button class="md:hidden">-->
<!--                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"-->
<!--                     stroke="currentColor">-->
<!--                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>-->
<!--                </svg>-->
<!--            </button>-->
<!--        </div>-->
<!--    </header>-->
<!--</template>-->

<!--<script setup>-->
<!--import {Link} from '@inertiajs/vue3';-->
<!--</script>-->
<template>
    <header class="bg-[#2c3e50] text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-400" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <h1 class="text-xl font-serif font-bold">Academia Inspired</h1>
            </div>
            <div class="hidden md:flex items-center space-x-6">
                <Link href="/" class="hover:text-amber-300 transition-colors duration-300">Home</Link>
                <Link href="/books" class="hover:text-amber-300 transition-colors duration-300">Browse Books</Link>
                <Link href="/borrows" class="hover:text-amber-300 transition-colors duration-300">My Books</Link>
            </div>
            <div class="flex items-center space-x-4">
                <button class="p-2 rounded-full hover:bg-[#34495e] transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
                <div class="relative">
                    <button class="p-2 rounded-full hover:bg-[#34495e] transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-amber-400"></span>
                    </button>
                </div>
                <div class="relative">
                    <button class="flex items-center space-x-2 focus:outline-none">
                        <div
                            class="h-8 w-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-medium">
                            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                        </div>
                        <span class="hidden md:inline">{{ $page.props.auth.user.name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 hidden md:inline" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                        <Link href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile
                        </Link>
                        <Link href="/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Settings
                        </Link>
                        <Link href="/logout" method="post" as="button"
                              class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import {Link} from '@inertiajs/vue3';
</script>
