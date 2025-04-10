<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Header/>

        <main class="container mx-auto px-4 py-12 max-w-7xl">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-serif font-bold text-[#2c3e50] mb-4">Library Blog</h1>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Discover the latest news, book recommendations, and library updates.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                <div v-for="post in posts" :key="post.id"
                     class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <img :src="post.image" :alt="post.title" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="flex items-center text-sm text-gray-500 mb-2">
                            <span>{{ post.date }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ post.category }}</span>
                        </div>
                        <h3 class="text-xl font-serif font-bold text-[#2c3e50] mb-3">{{ post.title }}</h3>
                        <p class="text-gray-600 mb-4">{{ post.excerpt }}</p>
                        <Link :href="`/blog/${post.slug}`"
                              class="text-amber-600 hover:text-amber-700 font-medium inline-flex items-center">
                            Read more
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <Link href="/blog/archive"
                      class="bg-[#2c3e50] hover:bg-[#34495e] text-white px-6 py-3 rounded-lg font-medium inline-flex items-center">
                    View All Posts
                </Link>
            </div>
        </main>

        <Footer :year="currentYear"/>
    </div>
</template>

<script setup>
import {ref, onMounted} from 'vue';
import Header from '@/Components/AppHeader.vue';
import Footer from '@/Components/AppFooter.vue';
import {Link} from '@inertiajs/vue3';

const currentYear = ref(new Date().getFullYear());

const posts = [
    {
        id: 1,
        title: 'Summer Reading Challenge 2023',
        excerpt: 'Join our annual summer reading challenge with exciting prizes for all age groups.',
        category: 'Events',
        date: 'June 15, 2023',
        image: '/images/blog/summer-reading.jpg',
        slug: 'summer-reading-challenge'
    },
    {
        id: 2,
        title: 'Hidden Gems: Underrated Books You Should Read',
        excerpt: 'Discover these lesser-known masterpieces that deserve more attention.',
        category: 'Recommendations',
        date: 'May 28, 2023',
        image: '/images/blog/hidden-gems.jpg',
        slug: 'hidden-gems'
    },
    {
        id: 3,
        title: 'The Future of Digital Libraries',
        excerpt: 'How technology is transforming the way we access and interact with books.',
        category: 'Technology',
        date: 'April 10, 2023',
        image: '/images/blog/digital-library.jpg',
        slug: 'future-digital-libraries'
    }
];
</script>
