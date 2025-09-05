<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Header/>

        <main class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- Hero Section -->
            <div class="mb-12 bg-[#e8e3d5] rounded-xl p-8">
                <div class="text-center">
                    <h2 class="text-4xl font-serif font-bold text-[#2c3e50] mb-4">Explore by Genre</h2>
                    <p class="text-lg text-gray-700 max-w-3xl mx-auto">
                        Discover books across different genres. From captivating fiction to informative non-fiction,
                        find your perfect read by browsing our organized collection.
                    </p>
                </div>
            </div>

            <!-- Search Section -->
            <div class="mb-8 bg-white rounded-xl shadow-sm p-6 border border-[#e8e3d5]">
                <div class="relative max-w-md mx-auto">
                    <input
                        type="text"
                        placeholder="Search genres..."
                        class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300"
                        v-model="searchQuery"
                        @input="filterGenres"
                    />
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 absolute left-3 top-3.5"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Genres Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div
                    v-for="genre in filteredGenres"
                    :key="genre.id"
                    class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-[#e8e3d5] cursor-pointer group"
                    @click="navigateToGenre(genre)"
                >
                    <div class="p-6">
                        <!-- Genre Icon & Color -->
                        <div class="flex items-center justify-center mb-4">
                            <div :class="`${genre.color} rounded-full p-4 text-white text-3xl group-hover:scale-110 transition-transform duration-300`">
                                {{ genre.icon }}
                            </div>
                        </div>

                        <!-- Genre Info -->
                        <div class="text-center">
                            <h3 class="text-xl font-semibold text-[#2c3e50] mb-2 group-hover:text-amber-600 transition-colors">
                                {{ genre.name }}
                            </h3>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-2" v-if="genre.description">
                                {{ genre.description }}
                            </p>

                            <div class="flex items-center justify-center gap-2 text-amber-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z" />
                                </svg>
                                <span class="font-medium">{{ genre.books_count }} books</span>
                            </div>
                        </div>
                    </div>

                    <!-- Hover Effect -->
                    <div class="bg-gradient-to-t from-amber-50 to-transparent p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="flex items-center justify-center text-amber-600">
                            <span class="text-sm font-medium">Browse {{ genre.name }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="filteredGenres.length === 0" class="text-center py-12">
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0012 15c-2.34 0-4.469-.634-6.322-1.736M4 7h16M4 7a1 1 0 011-1h14a1 1 0 011 1v10a1 1 0 01-1 1H5a1 1 0 01-1-1V7z" />
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">No genres found</h3>
                <p class="text-gray-600">{{ searchQuery ? 'Try adjusting your search terms.' : 'No genres are currently available.' }}</p>
            </div>

            <!-- Stats Section -->
            <div class="mt-16 bg-white rounded-xl shadow-sm p-8 border border-[#e8e3d5]">
                <div class="text-center">
                    <h3 class="text-2xl font-serif font-bold text-[#2c3e50] mb-6">Collection Overview</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-amber-600 mb-2">{{ genres.length }}</div>
                            <div class="text-gray-600">Total Genres</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-amber-600 mb-2">{{ totalBooks }}</div>
                            <div class="text-gray-600">Total Books</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-amber-600 mb-2">{{ Math.round(totalBooks / genres.length) }}</div>
                            <div class="text-gray-600">Avg Books per Genre</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import Header from '@/Components/AppHeader.vue'

const props = defineProps({
    genres: Array
})

const searchQuery = ref('')
const filteredGenres = ref([...props.genres])

const totalBooks = computed(() => {
    return props.genres.reduce((sum, genre) => sum + genre.books_count, 0)
})

const filterGenres = () => {
    if (!searchQuery.value.trim()) {
        filteredGenres.value = [...props.genres]
        return
    }

    const query = searchQuery.value.toLowerCase()
    filteredGenres.value = props.genres.filter(genre =>
        genre.name.toLowerCase().includes(query) ||
        (genre.description && genre.description.toLowerCase().includes(query))
    )
}

const navigateToGenre = (genre) => {
    router.get(route('genres.show', genre.id))
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
