<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Header/>

        <main class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- Genre Header -->
            <div class="mb-8">
                <!-- Breadcrumb -->
                <nav class="mb-6">
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <a :href="route('home')" class="hover:text-amber-600 transition-colors">Home</a>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <a :href="route('genres.index')" class="hover:text-amber-600 transition-colors">Genres</a>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span class="font-medium text-[#2c3e50]">{{ genre.name }}</span>
                    </div>
                </nav>

                <!-- Genre Hero -->
                <div class="bg-[#e8e3d5] rounded-xl p-8 mb-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-6">
                            <div :class="`${genre.color} rounded-full p-6 text-white text-4xl`">
                                {{ genre.icon }}
                            </div>
                            <div>
                                <h1 class="text-4xl font-serif font-bold text-[#2c3e50] mb-2">{{ genre.name }}</h1>
                                <p class="text-lg text-gray-700 mb-4" v-if="genre.description">{{ genre.description }}</p>
                                <div class="flex items-center gap-2 text-amber-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z" />
                                    </svg>
                                    <span class="font-medium">{{ books.total }} books available</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="mb-8 bg-white rounded-xl shadow-sm p-6 border border-[#e8e3d5]">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <!-- Search -->
                    <div class="relative flex-grow max-w-md">
                        <input
                            type="text"
                            placeholder="Search books in this genre..."
                            class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300"
                            v-model="searchQuery"
                            @input="handleSearchInput"
                        />
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 absolute left-3 top-3.5"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <!-- Sort Options -->
                    <div class="flex gap-4">
                        <select
                            v-model="selectedSort"
                            @change="updateSort"
                            class="px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300"
                        >
                            <option v-for="option in sortOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>

                        <select
                            v-model="selectedOrder"
                            @change="updateSort"
                            class="px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300"
                        >
                            <option value="asc">A-Z</option>
                            <option value="desc">Z-A</option>
                        </select>

                        <select
                            v-model="selectedPerPage"
                            @change="updatePerPage"
                            class="px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300"
                        >
                            <option value="12">12 per page</option>
                            <option value="24">24 per page</option>
                            <option value="48">48 per page</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Books Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8" v-if="books.data.length > 0">
                <div
                    v-for="book in books.data"
                    :key="book.id"
                    class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-[#e8e3d5] cursor-pointer group"
                    @click="navigateToBook(book)"
                >
                    <!-- Book Cover -->
                    <div class="relative overflow-hidden">
                        <img
                            :src="book.cover_image_url || '/images/default-book-cover.jpg'"
                            :alt="book.title"
                            class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-300"
                        />
                        <div class="absolute top-3 right-3">
                            <span
                                :class="`px-2 py-1 text-xs font-medium rounded-full ${getStatusBadgeClass(book.status)}`"
                            >
                                {{ formatBookStatus(book.status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Book Info -->
                    <div class="p-4">
                        <h3 class="font-semibold text-[#2c3e50] mb-2 line-clamp-2 group-hover:text-amber-600 transition-colors">
                            {{ book.title }}
                        </h3>
                        <p class="text-gray-600 text-sm mb-2">by {{ book.author }}</p>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1" v-if="book.average_rating">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z"/>
                                </svg>
                                <span class="text-sm text-gray-600">{{ parseFloat(book.average_rating || 0)
                                .toFixed(1) || 'N/A' }}</span>
                            </div>

                            <div class="text-xs text-gray-500">
                                {{ book.publication_year }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z" />
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">No books found</h3>
                <p class="text-gray-600">No books are currently available in this genre.</p>
            </div>

            <!-- Pagination -->
            <div v-if="books.links && books.links.length > 3" class="flex justify-center">
                <nav class="flex items-center space-x-1">
                    <a
                        v-for="(link, index) in books.links"
                        :key="index"
                        :href="link.url"
                        @click.prevent="navigateToPage(link.url)"
                        :class="`px-3 py-2 text-sm rounded-md transition-colors ${
                            link.active
                                ? 'bg-amber-600 text-white'
                                : link.url
                                ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'
                                : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                        }`"
                        v-html="link.label"
                    ></a>
                </nav>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import Header from '@/Components/AppHeader.vue'

const props = defineProps({
    genre: Object,
    books: Object,
    filters: Object,
    sortOptions: Array
})

const searchQuery = ref(props.filters?.search || '')
const selectedSort = ref(props.filters?.sort || 'title')
const selectedOrder = ref(props.filters?.order || 'asc')
const selectedPerPage = ref(props.filters?.per_page || 12)

let searchTimeout = null

const handleSearchInput = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        updateFilters()
    }, 300)
}

const updateSort = () => {
    updateFilters()
}

const updatePerPage = () => {
    updateFilters()
}

const updateFilters = () => {
    const params = {
        search: searchQuery.value,
        sort: selectedSort.value,
        order: selectedOrder.value,
        per_page: selectedPerPage.value
    }

    // Remove empty parameters
    Object.keys(params).forEach(key => {
        if (!params[key]) delete params[key]
    })

    router.get(route('genres.show', props.genre.id), params, {
        preserveState: true,
        preserveScroll: true
    })
}

const navigateToPage = (url) => {
    if (url) {
        router.get(url)
    }
}

const navigateToBook = (book) => {
    router.get(route('books.show', book.id))
}

const getStatusBadgeClass = (status) => {
    switch(status) {
        case 'available':
            return 'bg-green-100 text-green-800'
        case 'borrowed':
            return 'bg-yellow-100 text-yellow-800'
        case 'reserved':
            return 'bg-blue-100 text-blue-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

const formatBookStatus = (status) => {
    switch(status) {
        case 'available':
            return 'Available'
        case 'borrowed':
            return 'Borrowed'
        case 'reserved':
            return 'Reserved'
        default:
            return status?.charAt(0)?.toUpperCase() + status?.slice(1) || 'Unknown'
    }
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
