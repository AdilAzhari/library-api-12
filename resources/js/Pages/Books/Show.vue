Copy
<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <AppHeader/>
        <main class="container mx-auto px-4 py-8 max-w-6xl">
            <!-- Flash Messages -->
            <div class="mb-6 space-y-3">
                <FlashMessage
                    v-if="flash.success"
                    type="success"
                    :message="flash.success"
                    @close="flash.success = null"
                />
                <FlashMessage
                    v-if="flash.error"
                    type="error"
                    :message="flash.error"
                    @close="flash.error = null"
                />
            </div>

            <!-- Book Details Section -->
            <section class="bg-white rounded-xl shadow-sm overflow-hidden border border-[#e8e3d5] mb-10">
                <!-- Book Spine Effect -->
                <div class="h-2 bg-gradient-to-r from-[#8b5a2b] to-[#d4a76a]"></div>

                <div class="md:flex">
                    <!-- Book Cover -->
                    <div class="md:w-2/5 bg-gradient-to-br from-[#f9f7f2] to-[#e8e3d5] relative">
                        <div class="p-8 sticky top-6">
                            <div
                                class="relative rounded-lg overflow-hidden aspect-[3/4] shadow-md border border-[#e8e3d5]">
                                <img
                                    :src="book.cover_image_url"
                                    :alt="book.title"
                                    class="absolute inset-0 w-full h-full object-cover"
                                    @error="handleImageError"
                                />
                                <div v-if="!book.cover_image_url"
                                     class="absolute inset-0 bg-gray-100 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div
                                    class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1 shadow-xs">
                                    <span class="text-sm font-medium text-[#8b5a2b]">{{ book.publication_year }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Book Information -->
                    <div class="p-8 md:w-3/5">
                        <div class="flex flex-col h-full justify-between space-y-8">
                            <div class="space-y-6">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h1 class="text-3xl font-serif font-bold text-[#2c3e50] leading-tight">
                                            {{ book.title }}</h1>
                                        <p class="mt-2 text-lg text-[#8b5a2b] flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#8b5a2b] mr-2"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            {{ book.author }}
                                        </p>
                                    </div>
                                    <span v-if="book.status === 'available'"
                                          class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Available
                                    </span>
                                    <span v-else
                                          class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        {{ book.status }}
                                    </span>
                                </div>

                                <div class="prose max-w-none text-gray-700">
                                    <p class="text-lg leading-relaxed">{{ book.description }}</p>
                                </div>

                                <!-- Book Metadata -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex items-center p-3 bg-[#f9f7f2] rounded-lg border border-[#e8e3d5]">
                                        <div class="bg-[#2c3e50] p-2 rounded-lg mr-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 6h3M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">ISBN</p>
                                            <p class="font-medium">{{ book.ISBN }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center p-3 bg-[#f9f7f2] rounded-lg border border-[#e8e3d5]">
                                        <div class="bg-[#8b5a2b] p-2 rounded-lg mr-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white"
                                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Genre</p>
                                            <p class="font-medium">{{ book.genre?.name || 'No genre' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-4">
                                <button
                                    @click="!isReserved ? showReservationModal = true : null"
                                    :disabled="isReserved || !book.is_available"
                                    :class="[
                                        'w-full inline-flex items-center justify-center rounded-xl px-6 py-4 text-base font-medium text-white shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200',
                                        isReserved || !book.is_available
                                            ? 'bg-gray-400 cursor-not-allowed'
                                            : 'bg-gradient-to-r from-[#2c3e50] to-[#34495e] hover:from-[#34495e] hover:to-[#2c3e50] focus:ring-[#2c3e50]'
                                    ]"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{
                                        isReserved
                                            ? 'Already Reserved'
                                            : book.is_available
                                                ? 'Reserve This Book'
                                                : 'Not Available'
                                    }}
                                </button>

                                <button
                                    v-if="book.status === 'available'"
                                    @click="borrowBook"
                                    :disabled="!book.is_available || isReserved"
                                    :class="['w-full inline-flex items-center justify-center rounded-xl px-6 py-4 text-base font-medium text-white shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200',
book.is_available && !isReserved ? 'bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 focus:ring-amber-500'
: 'bg-gray-400 cursor-not-allowed'
]"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    {{
                                        !book.is_available
                                            ? 'Not Available for Borrowing'
                                            : isReserved
                                                ? 'Cannot Borrow (Reserved)'
                                                : 'Borrow This Book'
                                    }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            Copy
            <!-- Reviews Section -->
            <section class="bg-white rounded-xl shadow-sm overflow-hidden border border-[#e8e3d5] mb-10">
                <div class="p-6 border-b border-[#e8e3d5] flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="bg-amber-100 p-2 rounded-lg mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-serif font-bold text-[#2c3e50]">Reader Reviews</h2>
                            <p class="text-sm text-gray-500">{{ book.reviews?.length || 0 }} reviews</p>
                        </div>
                    </div>
                    <button
                        @click="showReviewModal = true"
                        class="inline-flex items-center rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-[#2c3e50] border border-[#e8e3d5] hover:bg-[#f9f7f2] focus:outline-none focus:ring-2 focus:ring-amber-300 focus:ring-offset-2 transition-all duration-200 shadow-sm"
                        :disabled="hasReviewed"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        {{ hasReviewed ? 'Already Reviewed' : 'Write a Review' }}
                    </button>
                </div>

                <div class="divide-y divide-[#e8e3d5]">
                    <div v-if="!book.reviews || book.reviews.length === 0"
                         class="p-8 text-center text-gray-500 flex flex-col items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-2" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        No reviews yet. Be the first to share your thoughts!
                    </div>

                    <div v-for="review in book.reviews" :key="review.id"
                         class="p-6 hover:bg-[#f9f7f2]/50 transition-colors duration-200">
                        <div class="flex items-start">
                            <div
                                class="bg-gradient-to-br from-amber-100 to-amber-200 rounded-xl p-3 flex-shrink-0 shadow-sm">
                            <span class="font-medium text-amber-800 text-lg">
                                {{ review.user?.name?.charAt(0).toUpperCase() || 'A' }}
                            </span>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-medium text-[#2c3e50]">
                                        {{ review.user?.name || 'Anonymous Reader' }}
                                    </h3>
                                    <span class="text-sm text-gray-500 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ formatDate(review.created_at) }}
                                </span>
                                </div>
                                <div class="flex items-center mt-1">
                                    <div class="flex">
                                    <span v-for="i in 5" :key="i" class="text-amber-400">
                                        <svg v-if="i <= review.rating" class="h-5 w-5 fill-current"
                                             viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <svg v-else class="h-5 w-5 fill-current text-gray-300" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </span>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-500">{{ review.rating }} out of 5</span>
                                </div>
                                <p class="mt-3 text-gray-700 whitespace-pre-line">{{ review.comment }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Recommended Books Section -->
            <section v-if="recommendedBooks.length > 0"
                     class="bg-white rounded-xl shadow-sm overflow-hidden border border-[#e8e3d5]">
                <div class="p-6 border-b border-[#e8e3d5]">
                    <div class="flex items-center">
                        <div class="bg-[#8b5a2b] p-2 rounded-lg mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-serif font-bold text-[#2c3e50]">Recommended Books</h2>
                            <p class="text-sm text-gray-500">Similar books you might enjoy</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div v-for="book in recommendedBooks" :key="book.id"
                             class="group transition-all duration-200 hover:shadow-md rounded-lg overflow-hidden border border-[#e8e3d5]">
                            <div
                                class="relative rounded-lg overflow-hidden aspect-[3/4] bg-gradient-to-br from-[#f9f7f2] to-[#e8e3d5]">
                                <img
                                    :src="book.cover_image_url"
                                    :alt="book.title"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                />
                                <div
                                    class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm rounded-full px-2.5 py-1 shadow-xs">
                                    <span class="text-xs font-medium text-[#8b5a2b]">{{ book.publication_year }}</span>
                                </div>
                            </div>
                            <div class="mt-4 p-4">
                                <h3 class="text-lg font-serif font-medium text-[#2c3e50] line-clamp-1">{{
                                        book.title
                                    }}</h3>
                                <p class="text-sm text-[#8b5a2b] mt-1">{{ book.author }}</p>
                                <Link
                                    :href="`/books/${book.id}`"
                                    class="mt-3 inline-flex items-center text-sm font-medium text-[#2c3e50] hover:text-amber-600 transition-colors duration-200"
                                >
                                    View Details
                                    <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <Footer/>
        <!-- Reservation Modal -->
        <Modal :show="showReservationModal" @close="showReservationModal = false">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-[#2c3e50] p-2 rounded-lg mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-serif font-medium text-[#2c3e50]">Reserve Book</h2>
                </div>
                <p class="mt-2 text-gray-600">
                    You're about to reserve <span class="font-medium">"{{ book.title }}"</span>. This will hold the book
                    for you for 7 days.
                </p>
                <div class="mt-6 flex justify-end space-x-3">
                    <button
                        @click="showReservationModal = false"
                        type="button"
                        class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#2c3e50] focus:ring-offset-2 transition-all duration-200"
                    >
                        Cancel
                    </button>
                    <button
                        @click="reserveBook"
                        type="button"
                        class="px-4 py-2.5 text-sm font-medium text-white bg-[#2c3e50] border border-transparent rounded-lg shadow-sm hover:bg-[#34495e] focus:outline-none focus:ring-2 focus:ring-[#2c3e50] focus:ring-offset-2 transition-all duration-200"
                    >
                        Confirm Reservation
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Review Modal -->
        <Modal :show="showReviewModal" @close="showReviewModal = false">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-amber-100 p-2 rounded-lg mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-serif font-medium text-[#2c3e50]">Write a Review</h2>
                </div>

                <div class="mt-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <div class="flex items-center">
                            <div class="flex">
                                <button
                                    v-for="star in 5"
                                    :key="star"
                                    type="button"
                                    @click="reviewForm.rating = star"
                                    @mouseover="hoverRating = star"
                                    @mouseleave="hoverRating = 0"
                                    class="focus:outline-none"
                                >
                                    <svg
                                        :class="[
                                        'h-8 w-8',
                                        star <= (hoverRating || reviewForm.rating)
                                            ? 'text-amber-400 fill-current'
                                            : 'text-gray-300'
                                    ]"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-700">
                            {{
                                    reviewForm.rating > 0 ? `${reviewForm.rating} star${reviewForm.rating > 1 ? 's' : ''}` : 'Select rating'
                                }}
                        </span>
                        </div>
                    </div>

                    <div>
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                        <textarea
                            v-model="reviewForm.comment"
                            id="comment"
                            rows="4"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2c3e50] focus:ring-[#2c3e50] sm:text-sm"
                            placeholder="Share your thoughts about this book..."
                        ></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button
                            @click="showReviewModal = false"
                            type="button"
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#2c3e50] focus:ring-offset-2 transition-all duration-200"
                        >
                            Cancel
                        </button>
                        <button
                            @click="submitReview"
                            type="button"
                            :disabled="!reviewForm.rating || !reviewForm.comment.trim()"
                            :class="[
                            'px-4 py-2.5 text-sm font-medium text-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50] transition-all duration-200',
                            reviewForm.rating && reviewForm.comment.trim()
                                ? 'bg-[#2c3e50] hover:bg-[#34495e]'
                                : 'bg-gray-400 cursor-not-allowed'
                        ]"
                        >
                            Submit Review
                        </button>
                    </div>
                </div>
            </div>
        </Modal>
    </div>
</template>
<script setup> import {computed, reactive, ref} from 'vue';
import {Link, router, usePage} from '@inertiajs/vue3';
import AppHeader from '@/Components/AppHeader.vue';
import Footer from '@/Components/AppFooter.vue'
import FlashMessage from '@/Components/FlashMessage.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    book: Object,
    recommendedBooks: {type: Array, default: () => []},
    isReserved: Boolean,
    isBorrowed: Boolean,
    flash: Object
});
const page = usePage();
const flash = reactive({success: page.props.flash?.success || null, error: page.props.flash?.error || null});
const bookCover = computed(() => {
    return props.book.cover_image_url || '/images/book-cover-placeholder.jpg';
});
const handleImageError = (e) => {
    e.target.src = '/images/book-cover-placeholder.jpg';
};
const showReservationModal = ref(false);
const showReviewModal = ref(false);
const hoverRating = ref(0);
const hasReviewed = computed(() => {
    return props.book.reviews?.some(review => review.user_id === page.props.auth.user?.id);
});
const reviewForm = reactive({rating: 0, comment: ''});
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'});
};
const reserveBook = () => {
    router.post(`/books/${props.book.id}/reserve`, {}, {
        preserveScroll: true, onSuccess: () => {
            showReservationModal.value = false;
            flash.success = 'Book reserved successfully!';
            router.reload({only: ['book']});
        }, onError: (errors) => {
            showReservationModal.value = false;
            flash.error = errors.message || 'Failed to reserve book';
        }
    });
};
const borrowBook = () => {
    if (confirm('Are you sure you want to borrow this book?')) {
        router.post(`/books/${props.book.id}/borrow`, {}, {
            preserveScroll: true, onSuccess: () => {
                flash.success = 'Book borrowed successfully!';
                router.reload({only: ['book']});
            }, onError: (errors) => {
                flash.error = errors.message || 'Failed to borrow book';
            }
        });
    }
};
const submitReview = () => {
    if (!reviewForm.rating) {
        flash.error = 'Please select a rating';
        return;
    }
    if (!reviewForm.comment.trim()) {
        flash.error = 'Please write a review';
        return;
    }
    const reviewData = {rating: reviewForm.rating, comment: reviewForm.comment.trim()};
    router.post(`/books/${props.book.id}/reviews`, reviewData, {
        preserveScroll: true, onSuccess: () => {
            showReviewModal.value = false;
            reviewForm.rating = 0;
            reviewForm.comment = '';
            flash.success = 'Review submitted successfully!';
        }, onError: (errors) => {
            flash.error = errors.message || 'Failed to submit review';
        }
    });
};
const isAvailable = computed(() => {
    return props.book.status === 'available' && props.book.status !== 'borrowed' && props.book.status !== 'reserved';
}); </script>
<style> /* Custom scrollbar to match the theme */
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

/* Smooth transitions for interactive elements */
button, a, .transition-all {
    transition: all 0.2s ease;
}

/* Book card hover effect */
.book-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.book-card:hover {
    transform: translateY(-5px);
} </style>
