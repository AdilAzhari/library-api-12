<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Header/>

        <main class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- Hero Section -->
            <div class="mb-12 bg-gradient-to-r from-[#e8e3d5] via-amber-50 to-orange-50 rounded-xl p-8">
                <div class="flex flex-col lg:flex-row items-center justify-between">
                    <div class="lg:w-2/3 mb-6 lg:mb-0">
                        <h1 class="text-4xl font-serif font-bold text-[#2c3e50] mb-4">Discover Your Next Favorite Book</h1>
                        <p class="text-lg text-gray-700 mb-6">
                            {{ isAuthenticated
                                ? 'Personalized recommendations based on your reading history and preferences.'
                                : 'Curated book recommendations to help you discover your next great read.'
                            }}
                        </p>
                        <div class="flex items-center gap-6 text-sm">
                            <div class="flex items-center gap-2 text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span>Smart recommendations</span>
                            </div>
                            <div class="flex items-center gap-2 text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Quality curated</span>
                            </div>
                            <div class="flex items-center gap-2 text-purple-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span>Personalized</span>
                            </div>
                        </div>
                    </div>

                    <!-- User Stats Card (if authenticated) -->
                    <div class="lg:w-1/3 flex justify-center" v-if="isAuthenticated && userStats">
                        <div class="bg-white rounded-2xl p-6 shadow-lg border border-amber-200">
                            <h3 class="text-lg font-semibold text-[#2c3e50] mb-4 text-center">Your Reading Profile</h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Books read:</span>
                                    <span class="font-medium">{{ userStats.total_borrows }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Reviews written:</span>
                                    <span class="font-medium">{{ userStats.total_reviews }}</span>
                                </div>
                                <div class="flex justify-between" v-if="userStats.average_rating_given">
                                    <span class="text-gray-600">Avg. rating given:</span>
                                    <span class="font-medium">{{ userStats.average_rating_given }}/5</span>
                                </div>
                                <div class="pt-2 border-t" v-if="userStats.favorite_genre">
                                    <span class="text-gray-600">Favorite genre:</span>
                                    <div class="font-medium text-amber-600">{{ userStats.favorite_genre.name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="mb-8 bg-white rounded-xl shadow-sm p-6 border border-[#e8e3d5]">
                <div class="flex flex-wrap gap-3">
                    <button
                        v-for="cat in categories"
                        :key="cat.value"
                        @click="selectCategory(cat.value)"
                        :class="`px-4 py-2 rounded-lg font-medium transition-colors ${
                            selectedCategory === cat.value
                                ? 'bg-amber-600 text-white'
                                : 'bg-gray-100 text-gray-700 hover:bg-amber-100'
                        }`"
                    >
                        <span class="mr-2">{{ cat.icon }}</span>
                        {{ cat.label }}
                    </button>
                </div>
            </div>

            <!-- Recommendations Sections -->
            <div class="space-y-12">
                <!-- Personalized Recommendations -->
                <div v-if="shouldShowSection('personalized') && recommendations.personalized?.length > 0" class="recommendation-section">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-serif font-bold text-[#2c3e50] flex items-center gap-3">
                            <span class="text-2xl">👤</span>
                            Just For You
                        </h2>
                        <p class="text-gray-600 text-sm">Based on your reading history</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <BookCard v-for="book in recommendations.personalized" :key="'personalized-' + book.id" :book="book" />
                    </div>
                </div>

                <!-- Trending Books -->
                <div v-if="shouldShowSection('trending') && recommendations.trending?.length > 0" class="recommendation-section">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-serif font-bold text-[#2c3e50] flex items-center gap-3">
                            <span class="text-2xl">🔥</span>
                            Trending Now
                        </h2>
                        <p class="text-gray-600 text-sm">Most popular this month</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <BookCard v-for="book in recommendations.trending" :key="'trending-' + book.id" :book="book" />
                    </div>
                </div>

                <!-- Genre-based Recommendations -->
                <div v-if="shouldShowSection('genre_based') && recommendations.genre_based?.length > 0" class="recommendation-section">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-serif font-bold text-[#2c3e50] flex items-center gap-3">
                            <span class="text-2xl">📚</span>
                            {{ isAuthenticated ? 'More of Your Favorite Genres' : 'Popular Genres' }}
                        </h2>
                        <p class="text-gray-600 text-sm">
                            {{ isAuthenticated ? 'Based on genres you enjoy' : 'Books from popular categories' }}
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <BookCard v-for="book in recommendations.genre_based" :key="'genre-' + book.id" :book="book" />
                    </div>
                </div>

                <!-- Similar Users Recommendations -->
                <div v-if="shouldShowSection('similar_users') && recommendations.similar_users?.length > 0" class="recommendation-section">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-serif font-bold text-[#2c3e50] flex items-center gap-3">
                            <span class="text-2xl">👥</span>
                            Readers Like You Enjoyed
                        </h2>
                        <p class="text-gray-600 text-sm">Based on similar reading preferences</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <BookCard v-for="book in recommendations.similar_users" :key="'similar-' + book.id" :book="book" />
                    </div>
                </div>

                <!-- High-rated Books -->
                <div v-if="shouldShowSection('high_rated') && recommendations.high_rated?.length > 0" class="recommendation-section">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-serif font-bold text-[#2c3e50] flex items-center gap-3">
                            <span class="text-2xl">⭐</span>
                            Highly Rated
                        </h2>
                        <p class="text-gray-600 text-sm">Books with exceptional reviews</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <BookCard v-for="book in recommendations.high_rated" :key="'rated-' + book.id" :book="book" />
                    </div>
                </div>

                <!-- Recently Added Popular -->
                <div v-if="shouldShowSection('recently_added') && recommendations.recently_added?.length > 0" class="recommendation-section">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-serif font-bold text-[#2c3e50] flex items-center gap-3">
                            <span class="text-2xl">🆕</span>
                            New & Popular
                        </h2>
                        <p class="text-gray-600 text-sm">Recent additions getting great reviews</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <BookCard v-for="book in recommendations.recently_added" :key="'recent-' + book.id" :book="book" />
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!hasAnyRecommendations" class="text-center py-16">
                <div class="mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-medium text-gray-900 mb-3">No recommendations available</h3>
                <p class="text-gray-600 mb-6">
                    {{ isAuthenticated
                        ? 'Start borrowing and reviewing books to get personalized recommendations!'
                        : 'Sign in to get personalized recommendations based on your reading history.'
                    }}
                </p>
                <div class="flex justify-center gap-4">
                    <a :href="route('books.index')" class="bg-amber-600 text-white px-6 py-3 rounded-lg hover:bg-amber-700 transition-colors">
                        Browse All Books
                    </a>
                    <a v-if="!isAuthenticated" :href="route('login')" class="bg-white text-amber-600 px-6 py-3 rounded-lg border border-amber-600 hover:bg-amber-50 transition-colors">
                        Sign In
                    </a>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import Header from '@/Components/AppHeader.vue'

// Create a simplified BookCard component inline
const BookCard = {
    props: ['book'],
    template: `
        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-[#e8e3d5] cursor-pointer group"
             @click="navigateToBook">
            <div class="relative overflow-hidden">
                <img
                    :src="book.cover_image_url || '/images/default-book-cover.jpg'"
                    :alt="book.title"
                    class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <div class="absolute top-3 right-3">
                    <span :class="getStatusBadgeClass(book.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                        {{ formatBookStatus(book.status) }}
                    </span>
                </div>
            </div>

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
                        <span class="text-sm text-gray-600">{{ book.average_rating?.toFixed(1) || 'N/A' }}</span>
                    </div>

                    <div class="text-xs text-gray-500">
                        {{ book.publication_year }}
                    </div>
                </div>

                <span v-if="book.genre" class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">
                    {{ book.genre.name }}
                </span>
            </div>
        </div>
    `,
    methods: {
        navigateToBook() {
            router.get(route('books.show', this.book.id))
        },
        getStatusBadgeClass(status) {
            switch(status) {
                case 'available': return 'bg-green-100 text-green-800'
                case 'borrowed': return 'bg-yellow-100 text-yellow-800'
                case 'reserved': return 'bg-blue-100 text-blue-800'
                default: return 'bg-gray-100 text-gray-800'
            }
        },
        formatBookStatus(status) {
            switch(status) {
                case 'available': return 'Available'
                case 'borrowed': return 'Borrowed'
                case 'reserved': return 'Reserved'
                default: return status?.charAt(0)?.toUpperCase() + status?.slice(1) || 'Unknown'
            }
        }
    }
}

const props = defineProps({
    recommendations: Object,
    userStats: Object,
    filters: Object,
    categories: Array,
    isAuthenticated: Boolean
})

const selectedCategory = ref(props.filters?.category || 'all')

const hasAnyRecommendations = computed(() => {
    return Object.values(props.recommendations).some(section => section?.length > 0)
})

const shouldShowSection = (sectionName) => {
    return selectedCategory.value === 'all' || selectedCategory.value === sectionName
}

const selectCategory = (category) => {
    selectedCategory.value = category
    router.get(route('recommendations.index'), { category }, {
        preserveState: true,
        preserveScroll: true
    })
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.recommendation-section {
    background: white;
    border-radius: 0.75rem;
    padding: 2rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    border: 1px solid #e8e3d5;
}
</style>
