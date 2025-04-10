<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Header />

        <main class="container mx-auto px-4 py-8 max-w-4xl">
            <div class="bg-white rounded-xl shadow-md overflow-hidden p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-serif font-bold text-[#2c3e50] mb-4">Contact Us</h1>
                    <div class="w-24 h-1 bg-amber-400 mx-auto"></div>
                    <p class="text-gray-600 mt-4">Have questions? We'd love to hear from you.</p>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h2 class="text-xl font-serif text-[#2c3e50] mb-4">Get in Touch</h2>

                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="p-2 bg-amber-400 rounded-lg mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#2c3e50]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-[#2c3e50]">Email</h3>
                                    <p class="text-gray-600 text-sm">contact@academiainspired.com</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="p-2 bg-amber-400 rounded-lg mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#2c3e50]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-[#2c3e50]">Phone</h3>
                                    <p class="text-gray-600 text-sm">+1 (555) 123-4567</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="p-2 bg-amber-400 rounded-lg mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#2c3e50]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-[#2c3e50]">Address</h3>
                                    <p class="text-gray-600 text-sm">123 Library Lane<br>Bookville, BV 12345</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="font-serif text-[#2c3e50] mb-2">Follow Us</h3>
                            <div class="flex space-x-4">
                                <a href="#" class="text-[#2c3e50] hover:text-amber-500">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                                    </svg>
                                </a>
                                <a href="#" class="text-[#2c3e50] hover:text-amber-500">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                    </svg>
                                </a>
                                <a href="#" class="text-[#2c3e50] hover:text-amber-500">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl font-serif text-[#2c3e50] mb-4">Send Us a Message</h2>
                        <form class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring focus:ring-amber-200">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring focus:ring-amber-200">
                            </div>
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                                <input type="text" id="subject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring focus:ring-amber-200">
                            </div>
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                                <textarea id="message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring focus:ring-amber-200"></textarea>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-[#2c3e50] text-white rounded-md hover:bg-[#34495e] transition">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <AppFooter />
    </div>
</template>

<script setup>
import Header from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
</script>
