<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Header />

        <main class="container mx-auto px-4 py-12 max-w-5xl">
            <div class="bg-white rounded-xl shadow-md p-8">
                <h1 class="text-3xl font-serif font-bold text-[#2c3e50] mb-8">Website Sitemap</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="(section, index) in sitemap" :key="index" class="bg-gray-50 p-5 rounded-lg">
                        <h2 class="text-xl font-semibold text-[#2c3e50] mb-4 border-b border-gray-200 pb-2">{{ section.title }}</h2>
                        <ul class="space-y-2">
                            <li v-for="(link, linkIndex) in section.links" :key="linkIndex">
                                <Link :href="link.url" class="text-gray-600 hover:text-amber-600 flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 mt-0.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    {{ link.title }}
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>

        <Footer :year="currentYear" />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import Header from '@/Components/AppHeader.vue';
import Footer from '@/Components/AppFooter.vue';
import { Link } from '@inertiajs/vue3';

const currentYear = ref(new Date().getFullYear());

const sitemap = [
    {
        title: 'Discover',
        links: [
            { title: 'Browse Books', url: '/books' },
            { title: 'Book Genres', url: '/genres' },
            { title: 'New Releases', url: '/new-releases' },
            { title: 'Recommended Books', url: '/recommendations' }
        ]
    },
    {
        title: 'My Account',
        links: [
            { title: 'My Profile', url: '/profile' },
            { title: 'Borrowed Books', url: '/borrowed' },
            { title: 'My Reservations', url: '/reserved' },
            { title: 'My Reviews', url: '/reviews' }
        ]
    },
    {
        title: 'About Us',
        links: [
            { title: 'Our Story', url: '/about' },
            { title: 'Careers', url: '/careers' },
            { title: 'Blog', url: '/blog' },
            { title: 'Contact Us', url: '/contact' }
        ]
    },
    {
        title: 'Legal',
        links: [
            { title: 'Privacy Policy', url: '/privacy' },
            { title: 'Terms of Service', url: '/terms' },
            { title: 'Cookie Policy', url: '/cookies' },
            { title: 'Accessibility', url: '/accessibility' }
        ]
    }
];
</script>
