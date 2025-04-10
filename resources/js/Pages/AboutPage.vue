<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Header/>

        <main class="container mx-auto px-4 py-8 max-w-4xl">
            <div class="bg-white rounded-xl shadow-md overflow-hidden p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-serif font-bold text-[#2c3e50] mb-4">About Academia Inspired</h1>
                    <div class="w-24 h-1 bg-amber-400 mx-auto"></div>
                </div>

                <div class="prose max-w-none">
                    <div class="flex justify-center mb-8">
                        <div class="p-4 bg-amber-400 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-[#2c3e50]" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    </div>

                    <h2 class="text-xl font-serif text-[#2c3e50] mt-6">Our Mission</h2>
                    <p class="text-gray-700 mt-2">
                        Academia Inspired was founded in 2023 with a simple mission: to connect readers with the books
                        that inspire them.
                        We believe in the transformative power of literature and aim to make quality books accessible to
                        everyone.
                    </p>

                    <h2 class="text-xl font-serif text-[#2c3e50] mt-6">Our Collection</h2>
                    <p class="text-gray-700 mt-2">
                        With over 10,000 titles across various genres, our carefully curated collection includes
                        everything from classic
                        literature to contemporary works, academic texts to popular fiction. Each book is selected by
                        our team of
                        passionate bibliophiles.
                    </p>

                    <h2 class="text-xl font-serif text-[#2c3e50] mt-6">The Team</h2>
                    <p class="text-gray-700 mt-2">
                        Our team consists of librarians, educators, and technology experts working together to create a
                        seamless
                        library experience. We're united by our love for books and our commitment to serving the reading
                        community.
                    </p>

                    <div class="bg-[#e8e3d5] p-4 rounded-lg mt-8">
                        <h3 class="font-serif font-bold text-[#2c3e50]">Library Hours</h3>
                        <ul class="mt-2 space-y-1">
                            <li class="flex justify-between text-sm">
                                <span>Monday-Friday</span>
                                <span>9:00 AM - 8:00 PM</span>
                            </li>
                            <li class="flex justify-between text-sm">
                                <span>Saturday</span>
                                <span>10:00 AM - 6:00 PM</span>
                            </li>
                            <li class="flex justify-between text-sm">
                                <span>Sunday</span>
                                <span>12:00 PM - 5:00 PM</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>

        <AppFooter/>
    </div>
</template>

<script setup>
import Header from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
</script>
