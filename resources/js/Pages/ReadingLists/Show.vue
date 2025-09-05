<template>
    <AuthenticatedLayout>
        <Head :title="readingList.name"/>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <Link href="/reading-lists" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    ← Back to Reading Lists
                </Link>
                
                <div class="flex items-center justify-between mt-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                             :class="`bg-${readingList.color_theme || 'blue'}-100`">
                            <svg class="w-6 h-6" :class="`text-${readingList.color_theme || 'blue'}-600`" fill="currentColor" viewBox="0 0 20 20">
                                <path v-if="readingList.icon === 'book'" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                <path v-else-if="readingList.icon === 'star'" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                <path v-else-if="readingList.icon === 'heart'" fillRule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clipRule="evenodd"/>
                                <path v-else fillRule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clipRule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ readingList.name }}</h1>
                            <div class="flex items-center space-x-4 mt-2">
                                <div class="flex items-center space-x-2 text-sm text-gray-500">
                                    <span>{{ books.total }} books</span>
                                    <span v-if="readingList.is_default" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        Default
                                    </span>
                                    <span v-if="readingList.is_public" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        Public
                                    </span>
                                </div>
                                <div v-if="!isOwnList" class="text-sm text-gray-500">
                                    by {{ readingList.user.name }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-3">
                        <button v-if="!isOwnList" @click="duplicateList" 
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"/>
                                <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"/>
                            </svg>
                            Duplicate
                        </button>
                        
                        <Link v-if="isOwnList" :href="`/reading-lists/${readingList.id}/edit`"
                              class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                            </svg>
                            Edit List
                        </Link>

                        <button v-if="isOwnList" @click="showAddBookModal = true"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clipRule="evenodd"/>
                            </svg>
                            Add Book
                        </button>
                    </div>
                </div>

                <!-- Description -->
                <p v-if="readingList.description" class="mt-4 text-gray-600">
                    {{ readingList.description }}
                </p>

                <!-- Stats -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg shadow px-4 py-3">
                        <div class="text-sm font-medium text-gray-600">Total Books</div>
                        <div class="text-2xl font-bold text-blue-600">{{ books.total }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow px-4 py-3">
                        <div class="text-sm font-medium text-gray-600">Available</div>
                        <div class="text-2xl font-bold text-green-600">{{ stats.available_books }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow px-4 py-3">
                        <div class="text-sm font-medium text-gray-600">Borrowed</div>
                        <div class="text-2xl font-bold text-yellow-600">{{ stats.borrowed_books }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow px-4 py-3">
                        <div class="text-sm font-medium text-gray-600">Created</div>
                        <div class="text-sm font-medium text-gray-900">{{ formatDate(readingList.created_at) }}</div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between space-x-4">
                        <div class="flex-1 max-w-lg">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clipRule="evenodd"/>
                                    </svg>
                                </div>
                                <input v-model="searchQuery" @input="performSearch"
                                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Search books in this list...">
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <select v-model="filters.status" @change="applyFilters" class="rounded-md border-gray-300 text-sm">
                                <option value="all">All Books</option>
                                <option value="available">Available</option>
                                <option value="borrowed">Currently Borrowed</option>
                                <option value="reserved">Reserved</option>
                            </select>
                            <select v-model="filters.sort" @change="applyFilters" class="rounded-md border-gray-300 text-sm">
                                <option value="added_desc">Recently Added</option>
                                <option value="added_asc">Oldest First</option>
                                <option value="title_asc">Title A-Z</option>
                                <option value="title_desc">Title Z-A</option>
                                <option value="priority_desc">Priority</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Books Grid -->
            <div v-if="books.data && books.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                <div v-for="bookItem in books.data" :key="bookItem.id" 
                     class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200">
                    <div class="p-4">
                        <!-- Book Cover -->
                        <div class="aspect-w-2 aspect-h-3 mb-4">
                            <img v-if="bookItem.book.cover_image_url" 
                                 :src="bookItem.book.cover_image_url" 
                                 :alt="bookItem.book.title"
                                 class="w-full h-40 object-cover rounded">
                            <div v-else class="w-full h-40 bg-gray-100 rounded flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Book Info -->
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-900 line-clamp-2 mb-2">
                                {{ bookItem.book.title }}
                            </h3>
                            <p class="text-sm text-gray-600 line-clamp-1 mb-2">
                                {{ bookItem.book.author }}
                            </p>

                            <!-- Status Badge -->
                            <div class="flex items-center justify-between mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="{
                                          'bg-green-100 text-green-800': bookItem.book.status === 'available',
                                          'bg-yellow-100 text-yellow-800': bookItem.book.status === 'borrowed',
                                          'bg-blue-100 text-blue-800': bookItem.book.status === 'reserved'
                                      }">
                                    {{ bookItem.book.status.toUpperCase() }}
                                </span>
                                <div v-if="bookItem.priority" class="flex items-center">
                                    <svg v-for="star in 5" :key="star" 
                                         class="w-3 h-3" 
                                         :class="star <= bookItem.priority ? 'text-yellow-400' : 'text-gray-300'" 
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Notes -->
                            <p v-if="bookItem.notes" class="text-xs text-gray-600 mb-3 line-clamp-2">
                                {{ bookItem.notes }}
                            </p>

                            <!-- Actions -->
                            <div class="space-y-2">
                                <Link :href="`/books/${bookItem.book.id}`" 
                                      class="block w-full text-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    View Book
                                </Link>

                                <div class="flex space-x-2">
                                    <button v-if="bookItem.book.status === 'available'" @click="borrowBook(bookItem.book.id)"
                                            class="flex-1 px-3 py-2 text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Borrow
                                    </button>
                                    <button v-else-if="bookItem.book.status === 'borrowed'" @click="reserveBook(bookItem.book.id)"
                                            class="flex-1 px-3 py-2 text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Reserve
                                    </button>

                                    <button v-if="isOwnList" @click="removeFromList(bookItem.id)"
                                            class="px-3 py-2 text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No books in this list</h3>
                <p class="mt-1 text-sm text-gray-500">
                    {{ isOwnList ? 'Get started by adding your first book.' : 'This list is currently empty.' }}
                </p>
                <div v-if="isOwnList" class="mt-6">
                    <button @click="showAddBookModal = true"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fillRule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clipRule="evenodd"/>
                        </svg>
                        Add First Book
                    </button>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="books.data && books.data.length > 0" class="flex items-center justify-between">
                <p class="text-sm text-gray-700">
                    Showing {{ books.from }} to {{ books.to }} of {{ books.total }} results
                </p>
                <div class="flex space-x-2">
                    <Link v-if="books.prev_page_url" 
                          :href="books.prev_page_url" 
                          class="px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </Link>
                    <Link v-if="books.next_page_url" 
                          :href="books.next_page_url" 
                          class="px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </Link>
                </div>
            </div>
        </div>

        <!-- Add Book Modal -->
        <div v-if="showAddBookModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                        <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fillRule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clipRule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 text-center mt-4">Add Books to List</h3>
                    <p class="text-sm text-gray-500 text-center mt-2">
                        Search and add books to your reading list
                    </p>
                    
                    <!-- Book Search -->
                    <div class="mt-6">
                        <input v-model="bookSearch" @input="searchBooks" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Search for books...">
                        
                        <!-- Search Results -->
                        <div v-if="searchResults.length > 0" class="mt-3 max-h-48 overflow-y-auto border border-gray-200 rounded-md">
                            <div v-for="book in searchResults" :key="book.id" 
                                 class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100"
                                 @click="addBookToList(book)">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-10 bg-gray-100 rounded flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ book.title }}</p>
                                        <p class="text-sm text-gray-500 truncate">{{ book.author }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="items-center px-4 py-3 space-x-2 flex justify-end mt-6">
                        <button @click="showAddBookModal = false"
                                class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    readingList: Object,
    books: Object,
    stats: Object,
    user: Object,
    filters: Object,
})

const searchQuery = ref('')
const showAddBookModal = ref(false)
const bookSearch = ref('')
const searchResults = ref([])

const filters = ref({
    status: props.filters?.status || 'all',
    sort: props.filters?.sort || 'added_desc'
})

const isOwnList = computed(() => {
    return props.user && props.readingList.user_id === props.user.id
})

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', { 
        year: 'numeric',
        month: 'long', 
        day: 'numeric' 
    })
}

const performSearch = () => {
    // Debounced search implementation
    router.visit(`/reading-lists/${props.readingList.id}`, {
        data: { 
            search: searchQuery.value,
            status: filters.value.status,
            sort: filters.value.sort 
        },
        preserveState: true,
        preserveScroll: true,
    })
}

const applyFilters = () => {
    router.visit(`/reading-lists/${props.readingList.id}`, {
        data: { 
            search: searchQuery.value,
            status: filters.value.status,
            sort: filters.value.sort 
        },
        preserveState: true,
        preserveScroll: true,
    })
}

const searchBooks = () => {
    if (bookSearch.value.length < 2) {
        searchResults.value = []
        return
    }
    
    // Make API call to search books
    router.visit(`/api/books/search`, {
        data: { q: bookSearch.value },
        method: 'get',
        onSuccess: (data) => {
            searchResults.value = data.books || []
        }
    })
}

const addBookToList = (book) => {
    router.post(`/reading-lists/${props.readingList.id}/books/${book.id}`, {}, {
        onSuccess: () => {
            showAddBookModal.value = false
            bookSearch.value = ''
            searchResults.value = []
        }
    })
}

const removeFromList = (bookItemId) => {
    if (confirm('Are you sure you want to remove this book from the list?')) {
        router.delete(`/reading-lists/${props.readingList.id}/books/${bookItemId}`)
    }
}

const borrowBook = (bookId) => {
    router.post(`/books/${bookId}/borrow`)
}

const reserveBook = (bookId) => {
    router.post(`/reservations/books/${bookId}/reserve`)
}

const duplicateList = () => {
    router.post(`/reading-lists/${props.readingList.id}/duplicate`, {}, {
        onSuccess: () => {
            // Handle success
        }
    })
}
</script>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.aspect-w-2 {
    position: relative;
    padding-bottom: 150%;
}

.aspect-w-2 > * {
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}
</style>