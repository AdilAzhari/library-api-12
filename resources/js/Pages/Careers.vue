<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Header />

        <main class="container mx-auto px-4 py-12 max-w-7xl">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-serif font-bold text-[#2c3e50] mb-4">Join Our Team</h1>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    We're always looking for passionate individuals to help us build the future of digital libraries.
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-md p-8 mb-8">
                <h2 class="text-2xl font-serif font-bold text-[#2c3e50] mb-6">Current Openings</h2>

                <div class="space-y-6">
                    <div v-for="(position, index) in positions" :key="index" class="border-b border-gray-200 pb-6">
                        <h3 class="text-xl font-medium text-[#2c3e50]">{{ position.title }}</h3>
                        <div class="flex flex-wrap gap-4 mt-2 text-sm text-gray-500">
              <span class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                {{ position.type }}
              </span>
                            <span class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ position.location }}
              </span>
                        </div>
                        <p class="mt-3 text-gray-600">{{ position.description }}</p>
                        <Link :href="`/careers/${position.slug}`" class="inline-block mt-4 text-amber-600 hover:text-amber-700 font-medium">
                            Learn more & apply
                        </Link>
                    </div>
                </div>
            </div>

            <div class="bg-[#e8e3d5] rounded-xl p-8 text-center">
                <h2 class="text-2xl font-serif font-bold text-[#2c3e50] mb-4">Don't see your dream job?</h2>
                <p class="text-gray-600 mb-6">We're always interested in meeting talented people. Send us your resume!</p>
                <Link href="/contact" class="bg-[#2c3e50] hover:bg-[#34495e] text-white px-6 py-3 rounded-lg font-medium inline-flex items-center">
                    Contact Us
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </Link>
            </div>
        </main>

        <Footer :year="currentYear" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Header from '@/Components/AppHeader.vue';
import Footer from '@/Components/AppFooter.vue';
import { Link } from '@inertiajs/vue3';

const currentYear = ref(new Date().getFullYear());

const positions = [
    {
        title: 'Frontend Developer',
        type: 'Full-time',
        location: 'Remote',
        description: 'Help us build beautiful, responsive interfaces for our library platform using Vue.js and Tailwind CSS.',
        slug: 'frontend-developer'
    },
    {
        title: 'Librarian Consultant',
        type: 'Part-time',
        location: 'New York, NY',
        description: 'Provide expert guidance on book categorization and metadata management for our digital collection.',
        slug: 'librarian-consultant'
    },
    {
        title: 'Content Moderator',
        type: 'Contract',
        location: 'Remote',
        description: 'Ensure all user-generated content meets our community guidelines and quality standards.',
        slug: 'content-moderator'
    }
];
</script>
