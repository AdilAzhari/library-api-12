<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Header/>

        <main class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- Hero Section -->
            <div class="mb-12 bg-gradient-to-r from-[#e8e3d5] to-amber-100 rounded-xl p-8">
                <div class="flex flex-col lg:flex-row items-center justify-between">
                    <div class="lg:w-2/3 mb-6 lg:mb-0">
                        <h1 class="text-4xl font-serif font-bold text-[#2c3e50] mb-4">New Releases</h1>
                        <p class="text-lg text-gray-700 mb-6">
                            Discover the latest additions to our library collection. Stay up-to-date with fresh titles and trending reads.
                        </p>
                        <div class="flex items-center gap-6 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                <span>{{ stats.this_week }} added this week</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                <span>{{ stats.this_month }} added this month</span>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-1/3 flex justify-center">
                        <div class="bg-white rounded-2xl p-6 shadow-lg">
                            <div class="text-center">
                                <div class="text-3xl mb-2">📚</div>
                                <div class="text-2xl font-bold text-amber-600">{{ stats.total_new_books }}</div>
                                <div class="text-sm text-gray-600">New books in {{ selectedTimeframe }} days</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="mb-8 bg-white rounded-xl shadow-sm p-6 border border-[#e8e3d5]">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div class="lg:col-span-2 relative">
                        <input
                            type="text"
                            placeholder="Search new releases..."
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

                    <!-- Timeframe Filter -->
                    <select
                        v-model="selectedTimeframe"
                        @change="updateFilters"
                        class="px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300"
                    >
                        <option v-for="timeframe in timeframeOptions" :key="timeframe.value" :value="timeframe.value">
                            {{ timeframe.label }}
                        </option>
                    </select>

                    <!-- Genre Filter -->
                    <select
                        v-model="selectedGenre"
                        @change="updateFilters"
                        class="px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300"
                    >
                        <option value="">All Genres</option>
                        <option v-for="genre in genres" :key="genre.id" :value="genre.id">{{ genre.name }}</option>
                    </select>

                    <!-- Sort Options -->
                    <select
                        v-model="selectedSort"
                        @change="updateFilters"
                        class="px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300"
                    >
                        <option v-for="option in sortOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                </div>

                <!-- Additional Controls -->
                <div class="flex flex-col sm:flex-row justify-between items-center mt-4 pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-600 mb-2 sm:mb-0">
                        Showing {{ books.from || 0 }}-{{ books.to || 0 }} of {{ books.total }} results
                        <span v-if="stats.popular_genre" class="ml-4">
                            🔥 Popular genre: <strong>{{ stats.popular_genre.name }}</strong> ({{ stats.popular_genre.count }} books)
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-600">Per page:</label>
                        <select
                            v-model="selectedPerPage"
                            @change="updateFilters"
                            class="px-3 py-2 text-sm rounded border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300"
                        >
                            <option value="12">12</option>
                            <option value="24">24</option>
                            <option value="48">48</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Books Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8" v-if="books.data.length > 0">
                <div
                    v-for="book in books.data"
                    :key="book.id"
                    class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-[#e8e3d5] cursor-pointer group relative"
                    @click="navigateToBook(book)"
                >
                    <!-- New Badge -->
                    <div class="absolute top-3 left-3 z-10">
                        <span class="bg-amber-600 text-white px-2 py-1 text-xs font-medium rounded-full">
                            NEW
                        </span>
                    </div>

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

                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-1" v-if="book.average_rating">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z"/>
                                </svg>
                                <span class="text-sm text-gray-600">{{ parseFloat(book.average_rating || 0).toFixed(1) || 'N/A' }}</span>
                            </div>

                            <div class="text-xs text-gray-500">
                                {{ book.publication_year }}
                            </div>
                        </div>

                        <!-- Genre Badge -->
                        <div class="flex items-center justify-between">
                            <span v-if="book.genre" class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">
                                {{ book.genre.name }}
                            </span>

                            <span class="text-xs text-green-600 font-medium">
                                Added {{ formatDate(book.created_at) }}
                            </span>
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
                <h3 class="text-xl font-medium text-gray-900 mb-2">No new releases found</h3>
                <p class="text-gray-600">No books were added in the selected timeframe.</p>
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
    books: Object,
    genres: Array,
    stats: Object,
    filters: Object,
    sortOptions: Array,
    timeframeOptions: Array
})

const searchQuery = ref(props.filters?.search || '')
const selectedTimeframe = ref(props.filters?.timeframe || '30')
const selectedGenre = ref(props.filters?.genre || '')
const selectedSort = ref(props.filters?.sort || 'created_at')
const selectedPerPage = ref(props.filters?.per_page || 12)

let searchTimeout = null

const handleSearchInput = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        updateFilters()
    }, 300)
}

const updateFilters = () => {
    const params = {
        search: searchQuery.value,
        timeframe: selectedTimeframe.value,
        genre: selectedGenre.value,
        sort: selectedSort.value,
        per_page: selectedPerPage.value
    }

    // Remove empty parameters
    Object.keys(params).forEach(key => {
        if (!params[key]) delete params[key]
    })

    router.get(route('new-releases.index'), params, {
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

const formatDate = (dateString) => {
    const date = new Date(dateString)
    const now = new Date()
    const diffInDays = Math.floor((now - date) / (1000 * 60 * 60 * 24))

    if (diffInDays === 0) return 'today'
    if (diffInDays === 1) return 'yesterday'
    if (diffInDays < 7) return `${diffInDays} days ago`
    if (diffInDays < 30) return `${Math.floor(diffInDays / 7)} weeks ago`
    return `${Math.floor(diffInDays / 30)} months ago`
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
