<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Head title="Reading Lists"/>
        <Header/>
        
        <main class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Reading Lists</h1>
                    <p class="mt-2 text-gray-600">Organize your favorite books into custom lists</p>
                </div>
                <Link v-if="canCreateList" href="/reading-lists/create" 
                      class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clipRule="evenodd"/>
                    </svg>
                    New List
                </Link>
            </div>

            <!-- View Toggle & Stats -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-8">
                            <Link :href="`/reading-lists?view=my`" 
                                  :class="view === 'my' ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent'"
                                  class="border-b-2 py-2 px-1 text-sm font-medium hover:text-blue-600">
                                My Lists ({{ readingLists.total || 0 }})
                            </Link>
                            <Link :href="`/reading-lists?view=public`" 
                                  :class="view === 'public' ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent'"
                                  class="border-b-2 py-2 px-1 text-sm font-medium hover:text-blue-600">
                                Public Lists
                            </Link>
                            <Link :href="`/reading-lists?view=featured`" 
                                  :class="view === 'featured' ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent'"
                                  class="border-b-2 py-2 px-1 text-sm font-medium hover:text-blue-600">
                                Featured
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Stats for My Lists -->
                <div v-if="view === 'my' && stats" class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ stats.total_books }}</p>
                            <p class="text-xs text-gray-600">Total Books</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600">{{ stats.total_lists }}</p>
                            <p class="text-xs text-gray-600">Total Lists</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600">{{ stats.public_lists }}</p>
                            <p class="text-xs text-gray-600">Public Lists</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-600">Quick Actions</p>
                            <div class="flex space-x-2 mt-1">
                                <Link href="/reading-lists/create" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">
                                    + New
                                </Link>
                                <button class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                    Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reading Lists Grid -->
            <div v-if="readingLists.data && readingLists.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="list in readingLists.data" :key="list.id" 
                     class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <!-- List Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                     :class="`bg-${list.color_theme || 'blue'}-100`">
                                    <svg class="w-5 h-5" :class="`text-${list.color_theme || 'blue'}-600`" fill="currentColor" viewBox="0 0 20 20">
                                        <path v-if="list.icon === 'book'" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        <path v-else-if="list.icon === 'star'" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        <path v-else-if="list.icon === 'heart'" fillRule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clipRule="evenodd"/>
                                        <path v-else fillRule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clipRule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900 line-clamp-1">{{ list.name }}</h3>
                                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                                        <span>{{ list.books_count || 0 }} books</span>
                                        <span v-if="list.is_default" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            Default
                                        </span>
                                        <span v-if="list.is_public" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Public
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div v-if="view === 'my'" class="flex space-x-1">
                                <Link :href="`/reading-lists/${list.id}/edit`" 
                                      class="p-1 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    </svg>
                                </Link>
                            </div>
                        </div>

                        <!-- List Description -->
                        <p v-if="list.description" class="text-sm text-gray-600 mb-4 line-clamp-2">
                            {{ list.description }}
                        </p>

                        <!-- Creator Info for Public Lists -->
                        <div v-if="view !== 'my' && list.user" class="flex items-center text-xs text-gray-500 mb-4">
                            <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center mr-2">
                                <span class="text-xs font-medium">{{ list.user.name.charAt(0).toUpperCase() }}</span>
                            </div>
                            <span>by {{ list.user.name }}</span>
                        </div>

                        <!-- List Stats -->
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                            <span>Created {{ formatDate(list.created_at) }}</span>
                            <span v-if="list.updated_at !== list.created_at">
                                Updated {{ formatDate(list.updated_at) }}
                            </span>
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            <Link :href="`/reading-lists/${list.id}`" 
                                  class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                View List
                            </Link>
                            <button v-if="view !== 'my'" @click="duplicateList(list.id)" 
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"/>
                                    <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">
                    {{ view === 'my' ? 'No reading lists yet' : 'No lists found' }}
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    {{ view === 'my' ? 'Get started by creating your first reading list.' : 'Try a different view or check back later.' }}
                </p>
                <div v-if="view === 'my'" class="mt-6">
                    <Link href="/reading-lists/create" 
                          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fillRule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clipRule="evenodd"/>
                        </svg>
                        Create Reading List
                    </Link>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="readingLists.data && readingLists.data.length > 0" class="mt-8 flex items-center justify-between">
                <p class="text-sm text-gray-700">
                    Showing {{ readingLists.from }} to {{ readingLists.to }} of {{ readingLists.total }} results
                </p>
                <div class="flex space-x-2">
                    <Link v-if="readingLists.prev_page_url" 
                          :href="readingLists.prev_page_url" 
                          class="px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </Link>
                    <Link v-if="readingLists.next_page_url" 
                          :href="readingLists.next_page_url" 
                          class="px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </Link>
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        <Footer :year="new Date().getFullYear()" />
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import Header from '@/Components/AppHeader.vue'
import Footer from '@/Components/AppFooter.vue'

defineProps({
    readingLists: Object,
    stats: Object,
    view: String,
    canCreateList: Boolean,
})

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric' 
    })
}

const duplicateList = (listId) => {
    router.post(`/reading-lists/${listId}/duplicate`, {}, {
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
</style>