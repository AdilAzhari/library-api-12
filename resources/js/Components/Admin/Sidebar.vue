<template>
    <div class="fixed inset-y-0 left-0 z-30 w-72 bg-[#2c3e50] text-white transition-transform duration-300 ease-in-out"
         :class="{'transform -translate-x-full lg:translate-x-0': !isOpen, 'transform translate-x-0': isOpen}">
        <div class="flex items-center justify-between h-16 px-6 border-b border-gray-700">
            <span class="text-xl font-bold">Library Admin</span>
            <button @click="$emit('close')" class="lg:hidden text-gray-400 hover:text-white">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <nav class="mt-6">
            <div class="px-6 pb-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Navigation</p>
            </div>
            <ul class="space-y-1 px-2">
                <li>
                    <Link href="/admin/dashboard" class="flex items-center px-4 py-3 text-sm font-medium rounded-md hover:bg-gray-800"
                          :class="{'bg-gray-800': $page.url.startsWith('/admin/dashboard')}">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </Link>
                </li>
                <li>
                    <Link href="/admin/books" class="flex items-center px-4 py-3 text-sm font-medium rounded-md hover:bg-gray-800"
                          :class="{'bg-gray-800': $page.url.startsWith('/admin/books')}">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Book Management
                    </Link>
                </li>
                <li>
                    <Link href="/admin/reservations" class="flex items-center px-4 py-3 text-sm font-medium rounded-md hover:bg-gray-800"
                          :class="{'bg-gray-800': $page.url.startsWith('/admin/reservations')}">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Reservations
                    </Link>
                </li>
                <li>
                    <Link href="/admin/borrowings" class="flex items-center px-4 py-3 text-sm font-medium rounded-md hover:bg-gray-800"
                          :class="{'bg-gray-800': $page.url.startsWith('/admin/borrowings')}">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        Borrowings
                    </Link>
                </li>
                <li>
                    <Link href="/admin/users" class="flex items-center px-4 py-3 text-sm font-medium rounded-md hover:bg-gray-800"
                          :class="{'bg-gray-800': $page.url.startsWith('/admin/users')}">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        User Management
                    </Link>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    isOpen: Boolean
});

defineEmits(['close']);
</script>
