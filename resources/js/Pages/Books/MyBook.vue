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
                    <Link href="/" class="hover:text-amber-300 transition">Home</Link>
                    <Link href="/books" class="hover:text-amber-300 transition">Browse</Link>
                    <Link href="/my-books" class="text-amber-300 font-medium transition">My Books</Link>
                    <Link href="/profile" class="hover:text-amber-300 transition">Profile</Link>
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

        <main class="container mx-auto px-4 py-8 max-w-6xl">
            <!-- Page Title and Tabs -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
                <h2 class="text-3xl font-serif font-bold text-[#2c3e50]">My Books</h2>
                <div class="flex overflow-x-auto pb-2 md:pb-0">
                    <button
                        @click="activeTab = 'borrowed'"
                        :class="activeTab === 'borrowed' ? 'bg-[#2c3e50] text-white' : 'bg-white text-[#2c3e50]'"
                        class="px-4 py-2 rounded-lg border border-[#e8e3d5] shadow-sm whitespace-nowrap transition"
                    >
                        Borrowed ({{ borrowedBooks.length }})
                    </button>
                    <button
                        @click="activeTab = 'reserved'"
                        :class="activeTab === 'reserved' ? 'bg-[#2c3e50] text-white' : 'bg-white text-[#2c3e50]'"
                        class="px-4 py-2 rounded-lg border border-[#e8e3d5] shadow-sm whitespace-nowrap ml-2 transition"
                    >
                        Reserved ({{ reservedBooks.length }})
                    </button>
                    <button
                        @click="activeTab = 'history'"
                        :class="activeTab === 'history' ? 'bg-[#2c3e50] text-white' : 'bg-white text-[#2c3e50]'"
                        class="px-4 py-2 rounded-lg border border-[#e8e3d5] shadow-sm whitespace-nowrap ml-2 transition"
                    >
                        History
                    </button>
                </div>
            </div>

            <!-- Flash Messages -->
            <div class="mb-6 space-y-3">
                <FlashMessage
                    v-if="$page.props.flash.success"
                    type="success"
                    :message="$page.props.flash.success"
                    @close="$page.props.flash.success = null"
                />
                <FlashMessage
                    v-if="$page.props.flash.error"
                    type="error"
                    :message="$page.props.flash.error"
                    @close="$page.props.flash.error = null"
                />
            </div>

            <!-- Borrowed Books Section -->
            <section v-if="activeTab === 'borrowed'">
                <div v-if="borrowedBooks.length > 0" class="space-y-6">
                    <div
                        v-for="book in borrowedBooks"
                        :key="book.id"
                        class="bg-white rounded-xl shadow-sm overflow-hidden border border-[#e8e3d5] hover:shadow-md transition-all duration-300"
                    >
                        <div class="md:flex">
                            <!-- Book Cover -->
                            <div class="md:w-1/5 bg-gradient-to-br from-[#f9f7f2] to-[#e8e3d5] p-6">
                                <div
                                    class="relative rounded-lg overflow-hidden aspect-[3/4] shadow-md border border-[#e8e3d5]">
                                    <img
                                        :src="book.book.cover_image_url"
                                        :alt="book.book.title"
                                        class="absolute inset-0 w-full h-full object-cover"
                                        @error="handleImageError"
                                    >
                                </div>
                            </div>

                            <!-- Book Details -->
                            <div class="p-6 md:w-4/5">
                                <div class="flex flex-col h-full justify-between">
                                    <div class="space-y-4">
                                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                            <div>
                                                <h3 class="text-xl font-serif font-bold text-[#2c3e50]">
                                                    {{ book.book.title }}</h3>
                                                <p class="text-[#8b5a2b]">{{ book.book.author }}</p>
                                            </div>
                                            <span
                                                :class="getDueDateClass(book.due_date)"
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium self-start"
                                            >
                        Due {{ formatDate(book.due_date) }}
                      </span>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <!-- Borrowed Date -->
                                            <div
                                                class="flex items-center p-3 bg-[#f9f7f2] rounded-lg border border-[#e8e3d5]">
                                                <div class="bg-[#2c3e50] p-2 rounded-lg mr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white"
                                                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                         stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">Borrowed On</p>
                                                    <p class="font-medium">{{ formatDate(book.borrowed_at) }}</p>
                                                </div>
                                            </div>

                                            <!-- Days Remaining -->
                                            <div
                                                class="flex items-center p-3 bg-[#f9f7f2] rounded-lg border border-[#e8e3d5]">
                                                <div class="bg-[#8b5a2b] p-2 rounded-lg mr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white"
                                                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                         stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">Days Remaining</p>
                                                    <p class="font-medium">{{ daysRemaining(book.due_date) }}</p>
                                                </div>
                                            </div>

                                            <!-- Renewals -->
                                            <div
                                                class="flex items-center p-3 bg-[#f9f7f2] rounded-lg border border-[#e8e3d5]">
                                                <div class="bg-amber-500 p-2 rounded-lg mr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white"
                                                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                         stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">Renewals Used</p>
                                                    <p class="font-medium">{{ book.renewal_count }} of {{
                                                            maxRenewals
                                                        }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div
                                        class="flex flex-col-reverse md:flex-row justify-end space-y-4 md:space-y-0 space-y-reverse md:space-x-3 mt-6">
                                        <Link
                                            :href="`/books/${book.book.id}`"
                                            class="px-4 py-2 text-sm font-medium text-white bg-[#2c3e50] border border-transparent rounded-lg hover:bg-[#34495e] transition text-center"
                                        >
                                            View Details
                                        </Link>
                                        <button
                                            @click="renewBook(book)"
                                            :disabled="book.renewal_count >= maxRenewals || isOverdue(book.due_date)"
                                            :class="[
                        'px-4 py-2 text-sm font-medium rounded-lg transition text-center',
                        book.renewal_count >= maxRenewals || isOverdue(book.due_date)
                          ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                          : 'bg-white text-[#2c3e50] border border-[#2c3e50] hover:bg-[#2c3e50] hover:text-white'
                      ]"
                                        >
                                            Renew
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center border border-[#e8e3d5]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <h3 class="text-xl font-medium text-gray-700 mb-2">No Borrowed Books</h3>
                    <p class="text-gray-500 mb-4">You haven't borrowed any books yet.</p>
                    <Link href="/books"
                          class="inline-block bg-[#2c3e50] hover:bg-[#34495e] text-white font-medium px-6 py-3 rounded-lg transition">
                        Browse Books
                    </Link>
                </div>
            </section>

            <!-- Reserved Books Section -->
            <section v-if="activeTab === 'reserved'">
                <div v-if="reservedBooks.length > 0" class="space-y-6">
                    <div
                        v-for="book in reservedBooks"
                        :key="book.id"
                        class="bg-white rounded-xl shadow-sm overflow-hidden border border-[#e8e3d5] hover:shadow-md transition-all duration-300"
                    >
                        <div class="md:flex">
                            <!-- Book Cover -->
                            <div class="md:w-1/5 bg-gradient-to-br from-[#f9f7f2] to-[#e8e3d5] p-6">
                                <div
                                    class="relative rounded-lg overflow-hidden aspect-[3/4] shadow-md border border-[#e8e3d5]">
                                    <img
                                        :src="book.book.cover_image_url"
                                        :alt="book.book.title"
                                        class="absolute inset-0 w-full h-full object-cover"
                                        @error="handleImageError"
                                    >
                                </div>
                            </div>

                            <!-- Book Details -->
                            <div class="p-6 md:w-4/5">
                                <div class="flex flex-col h-full justify-between">
                                    <div class="space-y-4">
                                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                            <div>
                                                <h3 class="text-xl font-serif font-bold text-[#2c3e50]">
                                                    {{ book.book.title }}</h3>
                                                <p class="text-[#8b5a2b]">{{ book.book.author }}</p>
                                            </div>
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 self-start">
                        Reserved
                      </span>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <!-- Reserved Date -->
                                            <div
                                                class="flex items-center p-3 bg-[#f9f7f2] rounded-lg border border-[#e8e3d5]">
                                                <div class="bg-[#2c3e50] p-2 rounded-lg mr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white"
                                                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                         stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">Reserved On</p>
                                                    <p class="font-medium">{{ formatDate(book.created_at) }}</p>
                                                </div>
                                            </div>

                                            <!-- Expires In -->
                                            <div
                                                class="flex items-center p-3 bg-[#f9f7f2] rounded-lg border border-[#e8e3d5]">
                                                <div class="bg-[#8b5a2b] p-2 rounded-lg mr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white"
                                                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                         stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">Expires In</p>
                                                    <p class="font-medium">{{ daysRemaining(book.expires_at) }} days</p>
                                                </div>
                                            </div>

                                            <!-- Availability -->
                                            <div
                                                class="flex items-center p-3 bg-[#f9f7f2] rounded-lg border border-[#e8e3d5]">
                                                <div class="bg-amber-500 p-2 rounded-lg mr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white"
                                                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                         stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">Status</p>
                                                    <p class="font-medium">
                                                        {{ book.book.is_available ? 'Available' : 'Not Available' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div
                                        class="flex flex-col-reverse md:flex-row justify-end space-y-4 md:space-y-0 space-y-reverse md:space-x-3 mt-6">
                                        <Link
                                            :href="`/books/${book.book.id}`"
                                            class="px-4 py-2 text-sm font-medium text-white bg-[#2c3e50] border border-transparent rounded-lg hover:bg-[#34495e] transition text-center"
                                        >
                                            View Details
                                        </Link>
                                        <button
                                            @click="cancelReservation(book)"
                                            class="px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-600 rounded-lg hover:bg-red-50 transition text-center"
                                        >
                                            Cancel Reservation
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center border border-[#e8e3d5]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-xl font-medium text-gray-700 mb-2">No Reserved Books</h3>
                    <p class="text-gray-500 mb-4">You haven't reserved any books yet.</p>
                    <Link href="/books"
                          class="inline-block bg-[#2c3e50] hover:bg-[#34495e] text-white font-medium px-6 py-3 rounded-lg transition">
                        Browse Books
                    </Link>
                </div>
            </section>

            <!-- History Section -->
            <section v-if="activeTab === 'history'">
                <div v-if="history.data.length > 0"
                     class="bg-white rounded-xl shadow-sm overflow-hidden border border-[#e8e3d5]">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#e8e3d5]">
                            <thead class="bg-[#f9f7f2]">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-[#2c3e50] uppercase tracking-wider">
                                    Book
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-[#2c3e50] uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-[#2c3e50] uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-[#2c3e50] uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-[#e8e3d5]">
                            <tr v-for="item in history.data" :key="item.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded" :src="item.book.cover_image_url"
                                                 :alt="item.book.title">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-[#2c3e50]">{{ item.book.title }}</div>
                                            <div class="text-sm text-[#8b5a2b]">{{ item.book.author }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(item)"
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ getStatusText(item) }}
                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(item.borrowed_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <Link :href="`/books/${item.book.id}`"
                                          class="text-[#2c3e50] hover:text-amber-600 mr-4">View
                                    </Link>
                                    <button
                                        v-if="item.returned_at && item.book.is_available"
                                        @click="borrowAgain(item.book)"
                                        class="text-amber-600 hover:text-amber-800"
                                    >
                                        Borrow Again
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        class="bg-[#f9f7f2] px-6 py-4 border-t border-[#e8e3d5] flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-gray-500">
                            Showing <span class="font-medium">{{ history.from }}</span> to <span
                            class="font-medium">{{ history.to }}</span> of <span class="font-medium">{{
                                history.total
                            }}</span> results
                        </div>
                        <div class="flex space-x-2">
                            <Link
                                v-if="history.prev_page_url"
                                :href="`/my-books?tab=history&page=${history.current_page - 1}`"
                                class="px-3 py-1 rounded-md border border-[#e8e3d5] bg-white text-sm font-medium text-[#2c3e50] hover:bg-gray-50"
                            >
                                Previous
                            </Link>
                            <template v-for="page in history.links" :key="page.label">
                                <Link
                                    v-if="page.url && !page.active && page.label !== 'Next &raquo;' && page.label !== '&laquo; Previous'"
                                    :href="`/my-books?tab=history&page=${page.label}`"
                                    class="px-3 py-1 rounded-md border border-[#e8e3d5] bg-white text-sm font-medium text-[#2c3e50] hover:bg-gray-50"
                                >
                                    {{ page.label }}
                                </Link>
                                <span
                                    v-if="page.active"
                                    class="px-3 py-1 rounded-md border border-[#e8e3d5] bg-[#2c3e50] text-sm font-medium text-white"
                                >
                  {{ page.label }}
                </span>
                            </template>
                            <Link
                                v-if="history.next_page_url"
                                :href="`/my-books?tab=history&page=${history.current_page + 1}`"
                                class="px-3 py-1 rounded-md border border-[#e8e3d5] bg-white text-sm font-medium text-[#2c3e50] hover:bg-gray-50"
                            >
                                Next
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center border border-[#e8e3d5]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-xl font-medium text-gray-700 mb-2">No History Yet</h3>
                    <p class="text-gray-500 mb-4">Your borrowing history will appear here.</p>
                    <Link href="/books"
                          class="inline-block bg-[#2c3e50] hover:bg-[#34495e] text-white font-medium px-6 py-3 rounded-lg transition">
                        Browse Books
                    </Link>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <Footer/>
    </div>
</template>

<script setup>
import {ref, computed} from 'vue';
import {Link, router} from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';
import Footer from '@/Components/AppFooter.vue';

const props = defineProps({
    borrowedBooks: {
        type: Array,
        default: () => []
    },
    reservedBooks: {
        type: Array,
        default: () => []
    },
    history: {
        type: Object,
        default: () => ({data: []})
    },
    activeTab: {
        type: String,
        default: 'borrowed'
    }
});

const maxRenewals = 3; // Should match your config value

// Handle tab switching
const activeTab = ref(props.activeTab);

// Format date for display
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

// Calculate days remaining
const daysRemaining = (dateString) => {
    const today = new Date();
    const dueDate = new Date(dateString);
    const diffTime = dueDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays > 0 ? diffDays : 0;
};

// Check if book is overdue
const isOverdue = (dueDate) => {
    return new Date(dueDate) < new Date();
};

// Get due date badge class
const getDueDateClass = (dueDate) => {
    const days = daysRemaining(dueDate);
    if (isOverdue(dueDate)) {
        return 'bg-red-100 text-red-800';
    } else if (days <= 3) {
        return 'bg-amber-100 text-amber-800';
    } else {
        return 'bg-green-100 text-green-800';
    }
};

// Get status class for history items
const getStatusClass = (item) => {
    if (item.returned_at) {
        return 'bg-blue-100 text-blue-800';
    } else if (isOverdue(item.due_date)) {
        return 'bg-red-100 text-red-800';
    } else {
        return 'bg-green-100 text-green-800';
    }
};

// Get status text for history items
const getStatusText = (item) => {
    if (item.returned_at) {
        return 'Returned';
    } else if (isOverdue(item.due_date)) {
        return 'Overdue';
    } else {
        return 'Borrowed';
    }
};

// Handle image errors
const handleImageError = (e) => {
    e.target.src = '/images/book-cover-placeholder.jpg';
};

// Renew a book
const renewBook = (book) => {
    router.post(`/borrows/${book.id}/renew`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            // The page will refresh with updated data from the backend
        }
    });
};

// Cancel a reservation
const cancelReservation = (reservation) => {
    if (confirm('Are you sure you want to cancel this reservation?')) {
        router.delete(`/reservations/${reservation.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                // The page will refresh with updated data from the backend
            }
        });
    }
};

// Borrow a book again from history
const borrowAgain = (book) => {
    router.post(`/books/${book.id}/borrow-again`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            // The page will refresh with updated data from the backend
        }
    });
};
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

/* Table styling */
table {
    min-width: 100%;
}

table th {
    text-align: left;
    padding: 12px 16px;
}

table td {
    padding: 12px 16px;
    white-space: nowrap;
}
</style>
