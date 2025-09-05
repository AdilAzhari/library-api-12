<template>
    <AuthenticatedLayout>
        <Head title="Dashboard"/>

        <!-- Personal Greeting -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-8 mb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">{{ greeting }}</h1>
                        <p class="text-blue-100 mt-2">Welcome to your BiblioTech Hub dashboard</p>
                    </div>
                    <div class="hidden md:block">
                        <svg class="w-16 h-16 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Personal Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Active Borrows -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Active Borrows</p>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ personalStats.active_borrows.count }}/{{ personalStats.active_borrows.limit }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center justify-between">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full"
                                     :style="`width: ${(personalStats.active_borrows.count / personalStats.active_borrows.limit) * 100}%`"></div>
                            </div>
                        </div>
                        <Link href="/borrows" class="text-sm text-blue-600 hover:text-blue-800 mt-2 inline-block">
                            View all →
                        </Link>
                    </div>
                </div>

                <!-- Reading Lists -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clipRule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Reading Lists</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ personalStats.reading_lists.count }}</p>
                        </div>
                    </div>
                    <Link href="/reading-lists" class="text-sm text-green-600 hover:text-green-800 mt-4 inline-block">
                        Manage lists →
                    </Link>
                </div>

                <!-- Reviews Written -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Reviews</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ personalStats.reviews_written.count }}</p>
                            <p class="text-xs text-gray-500">Avg: {{ personalStats.reviews_written.average_rating }}★</p>
                        </div>
                    </div>
                    <Link href="/reviews" class="text-sm text-yellow-600 hover:text-yellow-800 mt-4 inline-block">
                        View reviews →
                    </Link>
                </div>

                <!-- Outstanding Fines -->
                <div class="bg-white rounded-lg shadow p-6" :class="personalStats.outstanding_fines.amount > 0 ? 'ring-2 ring-red-200' : ''">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8" :class="personalStats.outstanding_fines.amount > 0 ? 'text-red-600' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clipRule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Outstanding Fines</p>
                            <p class="text-2xl font-semibold" :class="personalStats.outstanding_fines.amount > 0 ? 'text-red-600' : 'text-gray-900'">
                                ${{ personalStats.outstanding_fines.amount.toFixed(2) }}
                            </p>
                            <p class="text-xs text-gray-500">{{ personalStats.outstanding_fines.count }} fines</p>
                        </div>
                    </div>
                    <Link v-if="personalStats.outstanding_fines.amount > 0" href="/fines" class="text-sm text-red-600 hover:text-red-800 mt-4 inline-block">
                        Pay fines →
                    </Link>
                </div>
            </div>

            <!-- Notifications -->
            <div v-if="notifications.length > 0" class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd"/>
                        </svg>
                        Notifications
                    </h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div v-for="notification in notifications" :key="notification.type"
                         class="flex items-start p-4 rounded-lg"
                         :class="{
                             'bg-red-50 border border-red-200': notification.priority === 'high',
                             'bg-yellow-50 border border-yellow-200': notification.priority === 'medium',
                             'bg-blue-50 border border-blue-200': notification.priority === 'low'
                         }">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5" :class="{
                                'text-red-600': notification.priority === 'high',
                                'text-yellow-600': notification.priority === 'medium',
                                'text-blue-600': notification.priority === 'low'
                            }" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h4 class="font-medium text-gray-900">{{ notification.title }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ notification.message }}</p>
                            <Link :href="notification.action_url" class="text-sm font-medium mt-2 inline-block"
                                  :class="{
                                      'text-red-600 hover:text-red-800': notification.priority === 'high',
                                      'text-yellow-600 hover:text-yellow-800': notification.priority === 'medium',
                                      'text-blue-600 hover:text-blue-800': notification.priority === 'low'
                                  }">
                                Take Action →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <Link href="/books" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <svg class="w-8 h-8 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">Browse Books</p>
                                <p class="text-sm text-gray-600">Find your next read</p>
                            </div>
                        </Link>

                        <Link href="/borrows" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <svg class="w-8 h-8 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clipRule="evenodd"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">My Borrows</p>
                                <p class="text-sm text-gray-600">{{ personalStats.active_borrows.count }} active</p>
                            </div>
                        </Link>

                        <Link href="/reading-lists" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                            <svg class="w-8 h-8 text-purple-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clipRule="evenodd"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">Reading Lists</p>
                                <p class="text-sm text-gray-600">{{ personalStats.reading_lists.count }} lists</p>
                            </div>
                        </Link>

                        <Link href="/reservations" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                            <svg class="w-8 h-8 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clipRule="evenodd"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">Reservations</p>
                                <p class="text-sm text-gray-600">{{ quickActions.ready_reservations }} ready</p>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Recommendations -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Activity -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                    </div>
                    <div class="px-6 py-4">
                        <!-- Recently Borrowed -->
                        <div v-if="recentActivity.recently_borrowed.length > 0" class="mb-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Recently Borrowed</h4>
                            <div class="space-y-2">
                                <div v-for="borrow in recentActivity.recently_borrowed" :key="borrow.id"
                                     class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 w-8 h-10 bg-blue-100 rounded flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ borrow.book.title }}</p>
                                        <p class="text-xs text-gray-500">{{ borrow.book.author }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Due Dates -->
                        <div v-if="recentActivity.upcoming_due_dates.length > 0">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Due Soon</h4>
                            <div class="space-y-2">
                                <div v-for="borrow in recentActivity.upcoming_due_dates" :key="borrow.id"
                                     class="flex items-center justify-between p-2 bg-yellow-50 rounded">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ borrow.book.title }}</p>
                                        <p class="text-xs text-gray-600">Due: {{ formatDate(borrow.due_date) }}</p>
                                    </div>
                                    <Link :href="`/borrows/${borrow.id}/return`"
                                          class="text-xs text-yellow-600 hover:text-yellow-800 font-medium">
                                        Return
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personalized Recommendations -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recommended for You</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div v-if="recommendations.length > 0" class="grid grid-cols-2 gap-4">
                            <div v-for="book in recommendations" :key="book.id"
                                 class="group cursor-pointer">
                                <Link :href="`/books/${book.id}`" class="block">
                                    <div class="aspect-w-3 aspect-h-4 bg-gray-200 rounded-md overflow-hidden group-hover:shadow-md transition-shadow">
                                        <img v-if="book.cover_image_url"
                                             :src="book.cover_image_url"
                                             :alt="book.title"
                                             class="w-full h-full object-center object-cover">
                                        <div v-else class="w-full h-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <h4 class="text-sm font-medium text-gray-900 line-clamp-2">{{ book.title }}</h4>
                                        <p class="text-xs text-gray-600 mt-1">{{ book.author }}</p>
                                        <div class="flex items-center mt-1">
                                            <span class="text-xs text-yellow-600">{{ book.average_rating }}★</span>
                                            <span class="text-xs text-gray-500 ml-2">{{ book.genre?.name || '' }}</span>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No recommendations yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Start borrowing books to get personalized recommendations</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineProps({
    personalStats: Object,
    recentActivity: Object,
    notifications: Array,
    quickActions: Object,
    recommendations: Array,
    greeting: String,
})

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric'
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

.aspect-w-3 {
    position: relative;
    width: 100%;
    height: 0;
    padding-bottom: 133.33%;
}

.aspect-w-3 > * {
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}
</style>
