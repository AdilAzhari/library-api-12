<script setup>
import {ref} from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import {Link, router} from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const searchQuery = ref('');

const performSearch = () => {
    if (searchQuery.value.trim()) {
        router.get(route('books.index'), {
            search: searchQuery.value.trim()
        });
    }
};
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-50">
            <!-- Navigation -->
            <nav class="bg-[#2c3e50] text-white shadow-lg">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">
                        <!-- Logo and Desktop Links -->
                        <div class="flex items-center">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <div class="flex items-center space-x-2">
                                        <div class="p-2 bg-amber-400 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#2c3e50]"
                                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                        <div class="hidden md:block">
                                            <span class="text-xl font-serif font-bold">BiblioTech Hub</span>
                                            <p class="text-xs text-amber-300">Your Gateway to Knowledge</p>
                                        </div>
                                    </div>
                                </Link>
                            </div>

                            <!-- Primary Navigation -->
                            <div class="hidden sm:ml-10 sm:block">
                                <div class="flex space-x-6">
                                    <NavLink
                                        :href="route('dashboard')"
                                        :active="route().current('dashboard')"
                                        class="hover:text-amber-300 transition-colors duration-200 flex items-center space-x-1"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"/>
                                        </svg>
                                        <span>Dashboard</span>
                                    </NavLink>

                                    <NavLink
                                        :href="route('books.index')"
                                        :active="route().current('books.*')"
                                        class="hover:text-amber-300 transition-colors duration-200 flex items-center space-x-1"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <span>Browse Books</span>
                                    </NavLink>

                                    <NavLink
                                        href="/genres"
                                        :active="route().current('genres.*')"
                                        class="hover:text-amber-300 transition-colors duration-200 flex items-center space-x-1"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7H3a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2z"/>
                                        </svg>
                                        <span>Genres</span>
                                    </NavLink>

                                    <NavLink
                                        href="/new-releases"
                                        :active="route().current('new-releases')"
                                        class="hover:text-amber-300 transition-colors duration-200 flex items-center space-x-1"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        <span>New Releases</span>
                                    </NavLink>

                                    <NavLink
                                        href="/recommendations"
                                        :active="route().current('recommendations')"
                                        class="hover:text-amber-300 transition-colors duration-200 flex items-center space-x-1"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                        </svg>
                                        <span>Recommendations</span>
                                    </NavLink>

                                    <NavLink
                                        :href="route('borrows.index')"
                                        :active="route().current('borrows.*')"
                                        class="hover:text-amber-300 transition-colors duration-200 flex items-center space-x-1"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <span>My Books</span>
                                    </NavLink>
                                </div>
                            </div>

                            <!-- Search Bar -->
                            <div class="hidden md:flex md:flex-1 md:justify-center md:ml-8">
                                <div class="w-full max-w-lg">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                        </div>
                                        <input
                                            type="text"
                                            placeholder="Search books, authors, genres..."
                                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-400 focus:outline-none focus:placeholder-gray-300 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 text-gray-900"
                                            @keyup.enter="performSearch"
                                            v-model="searchQuery"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Dropdown (Desktop) -->
                        <div class="hidden sm:ml-6 sm:block">
                            <div class="flex items-center">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button class="flex items-center space-x-2 focus:outline-none">
                                            <div
                                                class="h-8 w-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-medium">
                                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <span class="text-white">{{ $page.props.auth.user.name }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"
                                                      class="text-gray-700 hover:bg-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                            class="text-gray-700 hover:bg-gray-100"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Mobile menu button -->
                        <div class="sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-amber-300 focus:outline-none transition"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile Navigation -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden bg-[#34495e]"
                >
                    <div class="space-y-1 px-2 pt-2 pb-3">
                        <!-- Mobile Search -->
                        <div class="px-3 py-2">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    placeholder="Search books..."
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 text-gray-900 text-sm"
                                    @keyup.enter="performSearch"
                                    v-model="searchQuery"
                                />
                            </div>
                        </div>

                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                            class="text-white hover:bg-[#3d5166] hover:text-amber-300"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                        
                        <ResponsiveNavLink
                            :href="route('books.index')"
                            :active="route().current('books.*')"
                            class="text-white hover:bg-[#3d5166] hover:text-amber-300"
                        >
                            Browse Books
                        </ResponsiveNavLink>
                        
                        <ResponsiveNavLink
                            href="/genres"
                            :active="route().current('genres.*')"
                            class="text-white hover:bg-[#3d5166] hover:text-amber-300"
                        >
                            Genres
                        </ResponsiveNavLink>
                        
                        <ResponsiveNavLink
                            href="/new-releases"
                            :active="route().current('new-releases')"
                            class="text-white hover:bg-[#3d5166] hover:text-amber-300"
                        >
                            New Releases
                        </ResponsiveNavLink>
                        
                        <ResponsiveNavLink
                            href="/recommendations"
                            :active="route().current('recommendations')"
                            class="text-white hover:bg-[#3d5166] hover:text-amber-300"
                        >
                            Recommendations
                        </ResponsiveNavLink>
                        
                        <ResponsiveNavLink
                            :href="route('borrows.index')"
                            :active="route().current('borrows.*')"
                            class="text-white hover:bg-[#3d5166] hover:text-amber-300"
                        >
                            My Books
                        </ResponsiveNavLink>
                    </div>

                    <!-- Mobile User Menu -->
                    <div class="border-t border-[#3d5166] pt-4 pb-3">
                        <div class="flex items-center px-5">
                            <div
                                class="h-10 w-10 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-medium">
                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                            </div>
                            <div class="ml-3">
                                <div class="text-base font-medium text-white">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="text-sm font-medium text-gray-300">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1 px-2">
                            <ResponsiveNavLink
                                :href="route('profile.edit')"
                                class="text-white hover:bg-[#3d5166] hover:text-amber-300"
                            >
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="w-full text-left text-white hover:bg-[#3d5166] hover:text-amber-300"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Header -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header"/>
                </div>
            </header>

            <!-- Page Content -->
            <main class="bg-white">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot/>
                </div>
            </main>
        </div>
    </div>
</template>
