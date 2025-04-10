<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <!-- Header -->
        <header class="bg-[#2c3e50] text-white shadow-lg sticky top-0 z-50">
            <div class="container mx-auto px-4 py-3 flex justify-between items-center">
                <div class="flex items-center space-x-3 group">
                    <div class="p-2 bg-amber-400 rounded-lg group-hover:rotate-12 transition-transform duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#2c3e50]" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h1 class="text-xl font-serif font-bold tracking-tight">Academia Inspired</h1>
                </div>

                <nav class="hidden md:flex items-center space-x-6">
                    <Link href="/" class="px-3 py-2 rounded-lg bg-amber-400 text-[#2c3e50] font-medium">Home</Link>
                    <Link href="/books" class="px-3 py-2 rounded-lg hover:bg-[#34495e] hover:text-amber-300 transition">
                        Browse Books
                    </Link>
                    <Link href="/borrows"
                          class="px-3 py-2 rounded-lg hover:bg-[#34495e] hover:text-amber-300 transition">My Books
                    </Link>
                    <Link href="/about" class="px-3 py-2 rounded-lg hover:bg-[#34495e] hover:text-amber-300 transition">
                        About
                    </Link>
                </nav>

                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="p-2 rounded-full hover:bg-[#34495e] transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="relative">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            <div
                                class="h-8 w-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-medium">
                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-[#2c3e50] to-[#4a6491] text-white py-16">
            <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <h1 class="text-4xl md:text-5xl font-serif font-bold mb-4">Discover Your Next Adventure</h1>
                    <p class="text-xl mb-8">Explore our vast collection of books from around the world</p>
                    <div class="relative w-full max-w-md">
                        <input
                            type="text"
                            placeholder="Search by title, author or genre..."
                            class="w-full pl-12 pr-4 py-3 rounded-lg border-0 focus:ring-2 focus:ring-amber-300 bg-white bg-opacity-20 backdrop-filter backdrop-blur-sm text-white placeholder-gray-200"
                        />
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-300 absolute left-3 top-3.5"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="md:w-1/2 flex justify-center">
                    <img src="/images/hero-books.png" alt="Stack of books"
                         class="h-64 md:h-80 object-contain transform hover:scale-105 transition duration-500">
                </div>
            </div>
        </section>

        <!-- Featured Categories -->
        <section class="py-12 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-serif font-bold text-[#2c3e50] mb-8 text-center">Explore Categories</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div v-for="category in categories" :key="category.id"
                         class="bg-[#f9f7f2] rounded-xl p-6 text-center hover:shadow-lg transition cursor-pointer">
                        <div class="bg-amber-400 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#2c3e50]" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="font-serif font-bold text-[#2c3e50]">{{ category.name }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ category.count }} books</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- New Arrivals -->
        <section class="py-12 bg-[#e8e3d5]">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-serif font-bold text-[#2c3e50]">New Arrivals</h2>
                    <Link href="/books" class="text-amber-600 hover:underline flex items-center">
                        View all
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </Link>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div v-for="book in newArrivals" :key="book.id"
                         class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all duration-300">
                        <div class="h-48 overflow-hidden">
                            <img :src="book.cover_image" :alt="book.title"
                                 class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                        </div>
                        <div class="p-4">
                            <h3 class="font-serif font-bold text-[#2c3e50] line-clamp-2">{{ book.title }}</h3>
                            <p class="text-sm text-[#8b5a2b] mt-1">{{ book.author }}</p>
                            <div class="flex justify-between items-center mt-3">
                                <span class="text-xs bg-amber-100 text-amber-800 px-2 py-1 rounded-full">New</span>
                                <Link :href="`/books/${book.id}`" class="text-xs text-[#2c3e50] hover:text-amber-600">
                                    Details →
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Popular Books -->
        <section class="py-12 bg-white">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-serif font-bold text-[#2c3e50]">Most Popular</h2>
                    <Link href="/books" class="text-amber-600 hover:underline flex items-center">
                        View all
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </Link>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div v-for="book in popularBooks" :key="book.id"
                         class="flex bg-[#f9f7f2] rounded-lg overflow-hidden hover:shadow-md transition">
                        <div class="w-1/3">
                            <img :src="book.cover_image" :alt="book.title" class="w-full h-full object-cover">
                        </div>
                        <div class="w-2/3 p-4">
                            <h3 class="font-serif font-bold text-[#2c3e50]">{{ book.title }}</h3>
                            <p class="text-sm text-[#8b5a2b] mt-1">{{ book.author }}</p>
                            <div class="flex items-center mt-2">
                                <div class="flex">
                                    <svg v-for="i in 5" :key="i" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                         :class="i <= book.rating ? 'text-amber-400 fill-current' : 'text-gray-300'"
                                         viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-gray-500 ml-1">({{ book.reviews }})</span>
                            </div>
                            <p class="text-xs text-gray-600 mt-2 line-clamp-2">{{ book.description }}</p>
                            <Link :href="`/books/${book.id}`"
                                  class="inline-block mt-3 text-xs text-amber-600 hover:underline">View details →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="py-12 bg-[#2c3e50] text-white">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-serif font-bold mb-8 text-center">What Our Readers Say</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div v-for="testimonial in testimonials" :key="testimonial.id" class="bg-[#34495e] p-6 rounded-lg">
                        <div class="flex items-center mb-4">
                            <div
                                class="h-10 w-10 rounded-full bg-amber-400 flex items-center justify-center text-[#2c3e50] font-bold mr-3">
                                {{ testimonial.name.charAt(0) }}
                            </div>
                            <div>
                                <h4 class="font-medium">{{ testimonial.name }}</h4>
                                <p class="text-xs text-gray-300">{{ testimonial.role }}</p>
                            </div>
                        </div>
                        <p class="text-gray-200">"{{ testimonial.comment }}"</p>
                        <div class="flex mt-3">
                            <svg v-for="i in 5" :key="i" xmlns="http://www.w3.org/2000/svg"
                                 class="h-4 w-4 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="py-16 bg-gradient-to-r from-amber-400 to-amber-500">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl font-serif font-bold text-[#2c3e50] mb-4">Ready to start your reading journey?</h2>
                <p class="text-xl text-[#2c3e50] mb-8 max-w-2xl mx-auto">Join thousands of readers discovering new books
                    every day</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <Link href="/register"
                          class="px-6 py-3 bg-[#2c3e50] text-white rounded-lg hover:bg-[#34495e] transition font-medium">
                        Create Account
                    </Link>
                    <Link href="/books"
                          class="px-6 py-3 bg-white text-[#2c3e50] rounded-lg hover:bg-gray-100 transition font-medium">
                        Browse Books
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-[#2c3e50] text-white py-8">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-6 md:mb-0">
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-400" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <h2 class="text-xl font-serif font-bold">Academia Inspired</h2>
                        </div>
                        <p class="mt-2 text-sm text-gray-300">Your gateway to knowledge since 2023</p>
                    </div>
                    <div class="flex space-x-6">
                        <Link href="/about" class="hover:text-amber-300 transition">About</Link>
                        <Link href="/contact" class="hover:text-amber-300 transition">Contact</Link>
                        <Link href="/privacy" class="hover:text-amber-300 transition">Privacy</Link>
                        <Link href="/terms" class="hover:text-amber-300 transition">Terms</Link>
                    </div>
                </div>
                <div class="mt-8 pt-6 border-t border-gray-700 text-center text-sm text-gray-400">
                    &copy; 2023 Academia Inspired Library Management System. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import {Link} from '@inertiajs/vue3';

// Sample data - replace with actual data from your backend
const categories = [
    {id: 1, name: 'Fiction', count: 1245},
    {id: 2, name: 'Science', count: 876},
    {id: 3, name: 'History', count: 932},
    {id: 4, name: 'Biography', count: 567}
];

const newArrivals = [
    {
        id: 1,
        title: 'The Midnight Library',
        author: 'Matt Haig',
        cover_image: '/images/book1.jpg',
        description: 'A dazzling novel about all the choices that go into a life well lived.'
    },
    {
        id: 2,
        title: 'Project Hail Mary',
        author: 'Andy Weir',
        cover_image: '/images/book2.jpg',
        description: 'A lone astronaut must save the earth from disaster in this incredible new science-based thriller.'
    },
    {
        id: 3,
        title: 'Klara and the Sun',
        author: 'Kazuo Ishiguro',
        cover_image: '/images/book3.jpg',
        description: 'A magnificent new novel from the Nobel laureate Kazuo Ishiguro.'
    },
    {
        id: 4,
        title: 'The Code Breaker',
        author: 'Walter Isaacson',
        cover_image: '/images/book4.jpg',
        description: 'Jennifer Doudna, Gene Editing, and the Future of the Human Race.'
    }
];

const popularBooks = [
    {
        id: 5,
        title: 'Atomic Habits',
        author: 'James Clear',
        cover_image: '/images/book5.jpg',
        description: 'Tiny Changes, Remarkable Results: An Easy & Proven Way to Build Good Habits & Break Bad Ones',
        rating: 4,
        reviews: 128
    },
    {
        id: 6,
        title: 'Educated',
        author: 'Tara Westover',
        cover_image: '/images/book6.jpg',
        description: 'A memoir about a young girl who, kept out of school, leaves her survivalist family and goes on to earn a PhD from Cambridge University',
        rating: 5,
        reviews: 215
    },
    {
        id: 7,
        title: 'Sapiens',
        author: 'Yuval Noah Harari',
        cover_image: '/images/book7.jpg',
        description: 'A Brief History of Humankind',
        rating: 4,
        reviews: 342
    }
];

const testimonials = [
    {
        id: 1,
        name: 'Sarah Johnson',
        role: 'Book Club Leader',
        comment: 'Academia Inspired has transformed our reading group with its incredible selection and easy borrowing process.'
    },
    {
        id: 2,
        name: 'Michael Chen',
        role: 'University Professor',
        comment: 'The academic resources available here are unparalleled. My students and I use it regularly for research.'
    },
    {
        id: 3,
        name: 'Emma Rodriguez',
        role: 'Avid Reader',
        comment: 'I discover new favorite books every month thanks to their personalized recommendations. A reader\'s paradise!'
    }
];
</script>

<style>
/* Smooth transitions for hover effects */
.transition {
    transition: all 0.3s ease;
}

/* Book hover effect */
.hover\:shadow-xl {
    transition: box-shadow 0.3s ease, transform 0.3s ease;
}

.hover\:shadow-xl:hover {
    transform: translateY(-5px);
}

/* Hero search input backdrop */
.backdrop-filter {
    backdrop-filter: blur(10px);
}
</style>
