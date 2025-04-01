<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <!-- New Book-themed Header -->
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
                    <Link href="/borrows" class="hover:text-amber-300 transition">My Loans</Link>
                    <Link href="/reservations" class="hover:text-amber-300 transition">Reservations</Link>
                    <Link href="/books/create" class="bg-amber-500 hover:bg-amber-600 px-4 py-2 rounded transition">Add
                        Book
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

        <main class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- Hero Section -->
            <div class="mb-12 bg-[#e8e3d5] rounded-xl p-8 flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-1/2 mb-6 md:mb-0">
                    <h2 class="text-3xl font-serif font-bold text-[#2c3e50] mb-3">Discover Your Next Read</h2>
                    <p class="text-lg text-gray-700 mb-6">Browse our collection of carefully curated books from around
                        the world.</p>
                    <div class="relative w-full max-w-md">
                        <input
                            type="text"
                            placeholder="Search by title, author or genre..."
                            class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300"
                        />
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 absolute left-3 top-3.5"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="md:w-1/2 flex justify-center">
                    <img src="/images/library-hero.png" alt="Library illustration" class="h-64 object-contain">
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="mb-10 bg-white rounded-xl shadow-sm p-4 flex flex-col md:flex-row gap-4 items-center border border-[#e8e3d5]">
                <div class="flex flex-col md:flex-row w-full gap-4">
                    <div class="relative flex-grow">
                        <select
                            v-model="filters.genre"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300 appearance-none bg-white"
                        >
                            <option value="">All Genres</option>
                            <option v-for="genre in genres" :key="genre.id" :value="genre.id">{{ genre.name }}</option>
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5 text-gray-500 absolute right-3 top-3.5 pointer-events-none"
                             viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="relative flex-grow">
                        <select
                            v-model="filters.sort"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300 appearance-none bg-white"
                        >
                            <option value="">Sort By</option>
                            <option value="title_asc">Title (A-Z)</option>
                            <option value="title_desc">Title (Z-A)</option>
                            <option value="author_asc">Author (A-Z)</option>
                            <option value="author_desc">Author (Z-A)</option>
                            <option value="year_desc">Newest First</option>
                            <option value="year_asc">Oldest First</option>
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5 text-gray-500 absolute right-3 top-3.5 pointer-events-none"
                             viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="relative flex-grow">
                        <select
                            v-model="filters.status"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300 appearance-none bg-white"
                        >
                            <option value="">All Statuses</option>
                            <option value="available">Available</option>
                            <option value="borrowed">Borrowed</option>
                            <option value="reserved">Reserved</option>
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5 text-gray-500 absolute right-3 top-3.5 pointer-events-none"
                             viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <button
                        class="bg-[#2c3e50] text-white px-6 py-3 rounded-lg hover:bg-[#34495e] transition flex items-center justify-center"
                        @click="applyFilters"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Apply Filters
                    </button>
                    <button
                        v-if="hasFilters"
                        class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition flex items-center justify-center"
                        @click="resetFilters"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Reset
                    </button>
                </div>
            </div>

            <!-- Book Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <div
                    v-for="book in books"
                    :key="book.id"
                    class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col border border-[#e8e3d5]"
                >
                    <!-- Book Spine Effect -->
                    <div class="h-2 bg-gradient-to-r from-[#8b5a2b] to-[#d4a76a]"></div>

                    <div class="relative">
                        <div class="h-48 overflow-hidden">
                            <img
                                :src="book.cover_image_url"
                                :alt="book.title"
                                class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                            />
                            <div v-if="!book.cover_image_url"
                                 class="w-full h-full bg-gray-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                        </div>
                        <div class="absolute top-3 right-3 flex flex-col gap-1">
                            <span v-if="book.status === 'available'"
                                  class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M5 13l4 4L19 7"/>
                                </svg>
                                Available
                            </span>
                            <span v-else-if="book.status === 'borrowed'"
                                  class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                On Loan
                            </span>
                            <span v-else-if="book.status === 'reserved'"
                                  class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-1 rounded-full flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Reserved
                            </span>
                        </div>
                    </div>

                    <div class="p-5 flex-grow">
                        <div class="flex justify-between items-start mb-2">
                            <h2 class="text-lg font-serif font-bold text-gray-900 line-clamp-2">{{ book.title }}</h2>
<!--                            <span class="bg-[#e8e3d5] text-[#2c3e50] text-xs px-2 py-1 rounded font-medium">{{-->
<!--                                    book.genre.name-->
<!--                                }}</span>-->
                        </div>
                        <p class="text-[#8b5a2b] font-medium">{{ book.author }}</p>
                        <p class="text-xs text-gray-500 mt-1">Published {{ book.publication_year }}</p>

                        <div class="mt-3 flex items-center">
                            <div class="flex items-center mr-2">
                                <svg v-for="i in 5" :key="i" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                     :class="i <= (book.average_rating || 0) ? 'text-amber-400 fill-current' : 'text-gray-300'"
                                     viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-500">({{ book.reviews_count || 0 }} reviews)</span>
                        </div>

                        <p class="text-sm text-gray-600 mt-3 line-clamp-3">
                            {{ book.description || 'No description available.' }}
                        </p>
                    </div>

                    <div class="p-5 pt-0 border-t border-gray-100 mt-auto">
                        <div class="flex justify-between items-center">
                            <Link
                                :href="`/books/${book.id}`"
                                class="text-[#2c3e50] hover:text-amber-600 transition-colors duration-300 font-medium text-sm flex items-center"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Details
                            </Link>
                            <div class="flex space-x-2">
                                <button
                                    v-if="book.status === 'available'"
                                    @click="borrowBook(book.id)"
                                    class="bg-[#2c3e50] hover:bg-[#34495e] text-white px-3 py-1 rounded text-sm font-medium transition flex items-center"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    Borrow
                                </button>
                                <button
                                    v-else-if="book.status === 'borrowed'"
                                    @click="reserveBook(book.id)"
                                    class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded text-sm font-medium transition flex items-center"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Reserve
                                </button>
                                <span v-else class="text-xs text-gray-500 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ book.status === 'reserved' ? 'Reserved' : 'Unavailable' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-12 flex justify-center">
                <nav class="inline-flex rounded-md shadow-sm">
                    <button
                        @click="previousPage"
                        :disabled="currentPage === 1"
                        :class="{
                            'cursor-not-allowed opacity-50': currentPage === 1,
                            'hover:bg-gray-50': currentPage > 1
                        }"
                        class="px-4 py-2 rounded-l-md border border-gray-300 bg-white text-gray-700 flex items-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Prev
                    </button>
                    <template v-for="page in visiblePages" :key="page">
                        <button
                            v-if="page === '...'"
                            class="px-4 py-2 border-t border-b border-gray-300 bg-white text-gray-700"
                            disabled
                        >
                            ...
                        </button>
                        <button
                            v-else
                            @click="goToPage(page)"
                            :class="{
                                'bg-[#2c3e50] text-white': currentPage === page,
                                'text-gray-700 hover:bg-gray-50': currentPage !== page
                            }"
                            class="px-4 py-2 border-t border-b border-gray-300 bg-white"
                        >
                            {{ page }}
                        </button>
                    </template>
                    <button
                        @click="nextPage"
                        :disabled="currentPage === totalPages"
                        :class="{
                            'cursor-not-allowed opacity-50': currentPage === totalPages,
                            'hover:bg-gray-50': currentPage < totalPages
                        }"
                        class="px-4 py-2 rounded-r-md border border-gray-300 bg-white text-gray-700 flex items-center"
                    >
                        Next
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </nav>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-[#2c3e50] text-white py-8 mt-12">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-6 md:mb-0">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-400" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <h2 class="text-xl font-serif font-bold">Academia Inspired</h2>
                        </div>
                        <p class="mt-2 text-sm text-gray-300">Your gateway to knowledge since 2023</p>
                    </div>
                    <div class="flex space-x-6">
                        <a href="#" class="hover:text-amber-300 transition">About</a>
                        <a href="#" class="hover:text-amber-300 transition">Contact</a>
                        <a href="#" class="hover:text-amber-300 transition">Privacy Policy</a>
                        <a href="#" class="hover:text-amber-300 transition">Terms</a>
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
import {computed, reactive, ref, watch} from 'vue';
import {Link, router} from '@inertiajs/vue3';

const props = defineProps({
    books: {
        type: Array,
        default: () => []
    },
    genres: {
        type: Array,
        default: () => []
    },
    pagination: {
        type: Object,
        default: () => ({
            current_page: 1,
            last_page: 1,
            per_page: 12,
            total: 0
        })
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            genre: '',
            status: '',
            sort: ''
        })
    },
});

// Improved pagination with ellipsis
const visiblePages = computed(() => {
    const range = 2; // Number of pages to show around current page
    const pages = [];

    // Always show first page
    pages.push(1);

    // Show ellipsis if needed after first page
    if (currentPage.value - range > 2) {
        pages.push('...');
    }

    // Show pages around current page
    for (let i = Math.max(2, currentPage.value - range); i <= Math.min(totalPages.value - 1, currentPage.value + range); i++) {
        pages.push(i);
    }

    // Show ellipsis if needed before last page
    if (currentPage.value + range < totalPages.value - 1) {
        pages.push('...');
    }

    // Always show last page if there's more than one page
    if (totalPages.value > 1) {
        pages.push(totalPages.value);
    }

    return pages;
});

const borrowBook = (bookId) => {
    router.post(`/books/${bookId}/borrow`, {}, {
        onSuccess: () => {
            router.reload({only: ['books']});
        }
    });
};

const reserveBook = (bookId) => {
    router.post(`/books/${bookId}/reserve`, {}, {
        onSuccess: () => {
            router.reload({only: ['books']});
        }
    });
};
const currentPage = ref(props.pagination.current_page);
const totalPages = computed(() => props.pagination.last_page);

const filters = reactive({
    search: props.filters.search || '',
    genre: props.filters.genre || '',
    status: props.filters.status || '',
    sort: props.filters.sort || ''
});
const hasFilters = computed(() => {
    return filters.search || filters.genre || filters.status || filters.sort;
});

const applyFilters = () => {
    router.get(route('books.index'), {
        ...filters,
        page: 1 // Reset to first page when applying new filters
    }, {
        preserveState: true,
        replace: true
    });
};

const resetFilters = () => {
    filters.search = '';
    filters.genre = '';
    filters.status = '';
    filters.sort = '';
    applyFilters();
};

// Watch for filter changes and debounce the search
let searchTimeout = null;
watch(() => filters.search, (newVal) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});

// Watch other filters and apply immediately
watch(() => [filters.genre, filters.status, filters.sort], () => {
    applyFilters();
}, { deep: true });

const goToPage = (page) => {
    if (page !== currentPage.value && page !== '...') {
        router.get(route('books.index'), {page}, {
            preserveState: true,
            replace: true,
            onSuccess: () => {
                currentPage.value = page;
            }
        });
    }
};

const previousPage = () => {
    if (currentPage.value > 1) {
        goToPage(currentPage.value - 1);
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        goToPage(currentPage.value + 1);
    }
};
</script>

<style>
/* Smooth transitions for interactive elements */
button, a {
    transition: all 0.2s ease;
}

/* Book card hover effect */
.book-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.book-card:hover {
    transform: translateY(-5px);
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
