<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <!-- Header -->
        <header class="bg-[#2c3e50] text-white shadow-lg">
            <div class="container mx-auto px-4 py-6 flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-amber-400" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <h1 class="text-2xl font-serif font-bold">Academia Inspired</h1>
                </div>
                <nav class="hidden md:flex space-x-6">
                    <Link href="/books" class="hover:text-amber-300 transition">Browse Books</Link>
                    <Link href="/borrows" class="hover:text-amber-300 transition">My Loans</Link>
                    <Link href="/reservations" class="hover:text-amber-300 transition">Reservations</Link>
                    <Link href="/login" class="bg-amber-500 hover:bg-amber-600 px-4 py-2 rounded transition">Login
                    </Link>
                </nav>
                <button class="md:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="bg-gradient-to-b from-[#2c3e50] to-[#34495e] text-white py-20">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-4xl md:text-5xl font-serif font-bold mb-6">Discover Your Next Adventure</h1>
                <p class="text-xl max-w-2xl mx-auto mb-8">Explore our vast collection of books from around the world.
                    Knowledge awaits at your fingertips.</p>
                <Link href="/books"
                      class="inline-block bg-amber-500 hover:bg-amber-600 text-white font-medium px-8 py-4 rounded-lg shadow-lg transition duration-300">
                    Browse Collection
                </Link>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-serif font-bold text-center text-[#2c3e50] mb-12">Why Choose Our Library</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-[#f9f7f2] p-8 rounded-xl border border-[#e8e3d5] text-center">
                        <div
                            class="bg-[#2c3e50] text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-serif font-medium mb-3">Extensive Collection</h3>
                        <p class="text-gray-600">Over 50,000 books across all genres and subjects to satisfy every
                            reader's curiosity.</p>
                    </div>

                    <div class="bg-[#f9f7f2] p-8 rounded-xl border border-[#e8e3d5] text-center">
                        <div
                            class="bg-[#8b5a2b] text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-serif font-medium mb-3">Digital Resources</h3>
                        <p class="text-gray-600">Access thousands of e-books, audiobooks, and academic journals from
                            anywhere.</p>
                    </div>

                    <div class="bg-[#f9f7f2] p-8 rounded-xl border border-[#e8e3d5] text-center">
                        <div
                            class="bg-amber-500 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-serif font-medium mb-3">24/7 Access</h3>
                        <p class="text-gray-600">Our online portal is always open for your convenience, day or
                            night.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- New Arrivals Section -->
        <section class="py-16 bg-[#f9f7f2]">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center mb-10">
                    <h2 class="text-3xl font-serif font-bold text-[#2c3e50]">New Arrivals</h2>
                    <Link href="/books" class="text-amber-600 hover:text-amber-700 font-medium flex items-center">
                        View All
                        <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </Link>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
                    <!-- Book Card 1 -->
                    <div
                        class="bg-white rounded-lg shadow-sm overflow-hidden border border-[#e8e3d5] hover:shadow-md transition-all duration-300">
                        <div class="relative aspect-[3/4] bg-gradient-to-br from-[#f9f7f2] to-[#e8e3d5]">
                            <img
                                src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80"
                                alt="Book cover" class="absolute inset-0 w-full h-full object-cover">
                            <div
                                class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm rounded-full px-2 py-1 shadow-xs">
                                <span class="text-xs font-medium text-[#8b5a2b]">2023</span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-serif font-medium text-[#2c3e50] line-clamp-1">The Midnight Library</h3>
                            <p class="text-sm text-[#8b5a2b] mt-1">Matt Haig</p>
                        </div>
                    </div>

                    <!-- Book Card 2 -->
                    <div
                        class="bg-white rounded-lg shadow-sm overflow-hidden border border-[#e8e3d5] hover:shadow-md transition-all duration-300">
                        <div class="relative aspect-[3/4] bg-gradient-to-br from-[#f9f7f2] to-[#e8e3d5]">
                            <img
                                src="https://images.unsplash.com/photo-1589998059171-988d887df646?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80"
                                alt="Book cover" class="absolute inset-0 w-full h-full object-cover">
                            <div
                                class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm rounded-full px-2 py-1 shadow-xs">
                                <span class="text-xs font-medium text-[#8b5a2b]">2022</span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-serif font-medium text-[#2c3e50] line-clamp-1">Atomic Habits</h3>
                            <p class="text-sm text-[#8b5a2b] mt-1">James Clear</p>
                        </div>
                    </div>

                    <!-- Book Card 3 -->
                    <div
                        class="bg-white rounded-lg shadow-sm overflow-hidden border border-[#e8e3d5] hover:shadow-md transition-all duration-300">
                        <div class="relative aspect-[3/4] bg-gradient-to-br from-[#f9f7f2] to-[#e8e3d5]">
                            <img
                                src="https://images.unsplash.com/photo-1531346878377-a5be20888e57?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80"
                                alt="Book cover" class="absolute inset-0 w-full h-full object-cover">
                            <div
                                class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm rounded-full px-2 py-1 shadow-xs">
                                <span class="text-xs font-medium text-[#8b5a2b]">2023</span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-serif font-medium text-[#2c3e50] line-clamp-1">The Psychology of Money</h3>
                            <p class="text-sm text-[#8b5a2b] mt-1">Morgan Housel</p>
                        </div>
                    </div>

                    <!-- Book Card 4 -->
                    <div
                        class="bg-white rounded-lg shadow-sm overflow-hidden border border-[#e8e3d5] hover:shadow-md transition-all duration-300">
                        <div class="relative aspect-[3/4] bg-gradient-to-br from-[#f9f7f2] to-[#e8e3d5]">
                            <img
                                src="https://images.unsplash.com/photo-1541963463532-d68292c34b19?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80"
                                alt="Book cover" class="absolute inset-0 w-full h-full object-cover">
                            <div
                                class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm rounded-full px-2 py-1 shadow-xs">
                                <span class="text-xs font-medium text-[#8b5a2b]">2021</span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-serif font-medium text-[#2c3e50] line-clamp-1">The Silent Patient</h3>
                            <p class="text-sm text-[#8b5a2b] mt-1">Alex Michaelides</p>
                        </div>
                    </div>

                    <!-- Book Card 5 -->
                    <div
                        class="bg-white rounded-lg shadow-sm overflow-hidden border border-[#e8e3d5] hover:shadow-md transition-all duration-300">
                        <div class="relative aspect-[3/4] bg-gradient-to-br from-[#f9f7f2] to-[#e8e3d5]">
                            <img
                                src="https://images.unsplash.com/photo-1629992101753-56d196c8aabb?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80"
                                alt="Book cover" class="absolute inset-0 w-full h-full object-cover">
                            <div
                                class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm rounded-full px-2 py-1 shadow-xs">
                                <span class="text-xs font-medium text-[#8b5a2b]">2023</span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-serif font-medium text-[#2c3e50] line-clamp-1">Project Hail Mary</h3>
                            <p class="text-sm text-[#8b5a2b] mt-1">Andy Weir</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-serif font-bold text-center text-[#2c3e50] mb-12">What Our Readers Say</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-[#f9f7f2] p-8 rounded-xl border border-[#e8e3d5]">
                        <div class="flex items-center mb-4">
                            <div class="flex items-center text-amber-400 mr-2">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-gray-700 mb-6">"This library has transformed my reading habits. The collection is
                            incredible and the staff are so helpful!"</p>
                        <div class="flex items-center">
                            <div
                                class="bg-gradient-to-br from-amber-100 to-amber-200 rounded-full w-12 h-12 flex items-center justify-center mr-4">
                                <span class="font-medium text-amber-800 text-lg">S</span>
                            </div>
                            <div>
                                <h4 class="font-medium text-[#2c3e50]">Sarah Johnson</h4>
                                <p class="text-sm text-gray-500">Regular Member</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-[#f9f7f2] p-8 rounded-xl border border-[#e8e3d5]">
                        <div class="flex items-center mb-4">
                            <div class="flex items-center text-amber-400 mr-2">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-gray-700 mb-6">"The digital resources available through this library have been
                            invaluable for my research projects."</p>
                        <div class="flex items-center">
                            <div
                                class="bg-gradient-to-br from-amber-100 to-amber-200 rounded-full w-12 h-12 flex items-center justify-center mr-4">
                                <span class="font-medium text-amber-800 text-lg">M</span>
                            </div>
                            <div>
                                <h4 class="font-medium text-[#2c3e50]">Michael Chen</h4>
                                <p class="text-sm text-gray-500">Graduate Student</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-[#f9f7f2] p-8 rounded-xl border border-[#e8e3d5]">
                        <div class="flex items-center mb-4">
                            <div class="flex items-center text-amber-400 mr-2">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-5 h-5 fill-current text-gray-300" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-gray-700 mb-6">"My kids love the children's section and the weekly storytime
                            events. A wonderful community resource!"</p>
                        <div class="flex items-center">
                            <div
                                class="bg-gradient-to-br from-amber-100 to-amber-200 rounded-full w-12 h-12 flex items-center justify-center mr-4">
                                <span class="font-medium text-amber-800 text-lg">A</span>
                            </div>
                            <div>
                                <h4 class="font-medium text-[#2c3e50]">Amanda Rodriguez</h4>
                                <p class="text-sm text-gray-500">Parent</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="py-16 bg-gradient-to-r from-[#2c3e50] to-[#34495e] text-white">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl font-serif font-bold mb-6">Ready to Start Your Reading Journey?</h2>
                <p class="text-xl max-w-2xl mx-auto mb-8">Sign up for a free library card today and unlock a world of
                    knowledge.</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <Link href="/register"
                          class="inline-block bg-amber-500 hover:bg-amber-600 text-white font-medium px-8 py-4 rounded-lg shadow-lg transition duration-300">
                        Join Now
                    </Link>
                    <Link href="/books"
                          class="inline-block bg-transparent hover:bg-white/10 text-white font-medium px-8 py-4 rounded-lg border border-white transition duration-300">
                        Browse Collection
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <Footer/>
    </div>
</template>

<script setup>
import {Link} from '@inertiajs/vue3';
import Footer from '@/Components/AppFooter.vue';
</script>

<style>
/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f9f7f2;
}

::-webkit-scrollbar-thumb {
    background: #d4a76a;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #8b5a2b;
}

/* Smooth transitions */
button, a, .transition-all {
    transition: all 0.2s ease;
}

/* Book card hover effect */
.book-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.book-card:hover {
    transform: translateY(-5px);
}
</style>
