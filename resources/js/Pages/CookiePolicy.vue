<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Header />

        <main class="container mx-auto px-4 py-12 max-w-3xl">
            <div class="bg-white rounded-xl shadow-md p-8 md:p-10">
                <h1 class="text-3xl font-serif font-bold text-[#2c3e50] mb-6">Cookie Policy</h1>
                <p class="text-gray-600 mb-8">Last updated: {{ formattedDate }}</p>

                <div class="prose max-w-none">
                    <section class="mb-8">
                        <h2 class="text-xl font-semibold text-[#2c3e50] mb-4">What Are Cookies</h2>
                        <p class="text-gray-600 mb-4">
                            As is common practice with almost all professional websites, this site uses cookies, which are tiny files downloaded to your computer, to improve your experience. This page describes what information they gather, how we use it, and why we sometimes need to store these cookies.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-xl font-semibold text-[#2c3e50] mb-4">How We Use Cookies</h2>
                        <p class="text-gray-600 mb-4">
                            We use cookies for a variety of reasons detailed below. Unfortunately, in most cases there are no industry standard options for disabling cookies without completely disabling the functionality and features they add to this site.
                        </p>
                        <ul class="list-disc pl-5 space-y-2 text-gray-600">
                            <li>To enable certain functions of the website</li>
                            <li>To provide analytics on how our site is used</li>
                            <li>To store your preferences for future visits</li>
                            <li>For authentication and security purposes</li>
                        </ul>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-xl font-semibold text-[#2c3e50] mb-4">Disabling Cookies</h2>
                        <p class="text-gray-600 mb-4">
                            You can prevent the setting of cookies by adjusting the settings on your browser (see your browser's "Help" for how to do this). Be aware that disabling cookies will affect the functionality of this and many other websites that you visit.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-[#2c3e50] mb-4">More Information</h2>
                        <p class="text-gray-600">
                            If you have any questions about our cookie policy, please <Link href="/contact" class="text-amber-600 hover:text-amber-700">contact us</Link>.
                        </p>
                    </section>
                </div>
            </div>
        </main>

        <Footer :year="currentYear" />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import Header from '@/Components/AppHeader.vue';
import Footer from '@/Components/AppFooter.vue';
import { Link } from '@inertiajs/vue3';

const currentYear = ref(new Date().getFullYear());

const formattedDate = computed(() => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date().toLocaleDateString('en-US', options);
});
</script>
