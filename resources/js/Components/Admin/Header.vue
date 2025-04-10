<template>
    <header class="sticky top-0 z-10 bg-white shadow-sm">
        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
            <button @click="$emit('toggle-sidebar')" class="text-gray-500 hover:text-gray-600 lg:hidden">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="flex-1 flex justify-end items-center space-x-4">
                <button class="p-1 text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </button>

                <div class="relative ml-3">
                    <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-700">{{ $page.props.auth.user.name }}</span>
                        <img class="h-8 w-8 rounded-full"
                             :src="'https://ui-avatars.com/api/?name='+$page.props.auth.user.name" alt="User avatar">
                    </button>

                    <div v-show="userMenuOpen" @click.outside="userMenuOpen = false"
                         class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1 z-20">
                        <Link href="admin/profile"
                              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Your Profile
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
import {ref} from 'vue';
import {Link} from '@inertiajs/vue3';

const userMenuOpen = ref(false);
</script>
