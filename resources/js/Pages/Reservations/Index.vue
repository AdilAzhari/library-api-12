<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Head title="My Reservations"/>
        <Header/>
        
        <main class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">My Reservations</h1>
                <p class="mt-2 text-gray-600">Manage your current and past book reservations</p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clipRule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Active Reservations</p>
                            <p class="text-2xl font-semibold text-blue-600">{{ summary.active_reservations }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Ready for Pickup</p>
                            <p class="text-2xl font-semibold text-green-600">{{ summary.ready_for_pickup }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Expiring Soon</p>
                            <p class="text-2xl font-semibold text-yellow-600">{{ summary.expiring_soon }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clipRule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Queue Position</p>
                            <p class="text-2xl font-semibold text-gray-600">{{ summary.average_queue_position || 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-8">
                            <button @click="setFilter('active')" 
                                    :class="filter === 'active' ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent'"
                                    class="border-b-2 py-2 px-1 text-sm font-medium hover:text-blue-600">
                                Active ({{ summary.active_reservations }})
                            </button>
                            <button @click="setFilter('ready')" 
                                    :class="filter === 'ready' ? 'text-green-600 border-green-600' : 'text-gray-500 border-transparent'"
                                    class="border-b-2 py-2 px-1 text-sm font-medium hover:text-green-600">
                                Ready for Pickup ({{ summary.ready_for_pickup }})
                            </button>
                            <button @click="setFilter('expired')" 
                                    :class="filter === 'expired' ? 'text-red-600 border-red-600' : 'text-gray-500 border-transparent'"
                                    class="border-b-2 py-2 px-1 text-sm font-medium hover:text-red-600">
                                Expired
                            </button>
                            <button @click="setFilter('history')" 
                                    :class="filter === 'history' ? 'text-gray-600 border-gray-600' : 'text-gray-500 border-transparent'"
                                    class="border-b-2 py-2 px-1 text-sm font-medium hover:text-gray-600">
                                History
                            </button>
                        </div>
                        <div class="flex space-x-2">
                            <select v-model="sortBy" @change="applyFilters" class="rounded-md border-gray-300 text-sm">
                                <option value="created_at_desc">Recently Reserved</option>
                                <option value="created_at_asc">Oldest First</option>
                                <option value="expires_at_asc">Expiring Soon</option>
                                <option value="title_asc">Book Title A-Z</option>
                                <option value="queue_position_asc">Queue Position</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reservations List -->
            <div class="bg-white rounded-lg shadow">
                <div v-if="reservations.data && reservations.data.length > 0" class="divide-y divide-gray-200">
                    <div v-for="reservation in reservations.data" :key="reservation.id" class="px-6 py-6">
                        <div class="flex items-start space-x-4">
                            <!-- Book Cover -->
                            <div class="flex-shrink-0 w-16 h-20 bg-gray-100 rounded flex items-center justify-center">
                                <img v-if="reservation.book.cover_image_url" 
                                     :src="reservation.book.cover_image_url" 
                                     :alt="reservation.book.title"
                                     class="w-full h-full object-cover rounded">
                                <svg v-else class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>

                            <!-- Reservation Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-1">
                                            {{ reservation.book.title }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-2">
                                            by {{ reservation.book.author }}
                                        </p>
                                        
                                        <!-- Reservation Details -->
                                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-2">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clipRule="evenodd"/>
                                                </svg>
                                                Reserved: {{ formatDate(reservation.created_at) }}
                                            </div>
                                            
                                            <div v-if="reservation.expires_at" class="flex items-center" :class="{ 
                                                'text-red-600': isExpired(reservation.expires_at), 
                                                'text-yellow-600': isExpiringSoon(reservation.expires_at) && !isExpired(reservation.expires_at)
                                            }">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clipRule="evenodd"/>
                                                </svg>
                                                {{ isExpired(reservation.expires_at) ? 'Expired:' : 'Expires:' }} {{ formatDate(reservation.expires_at) }}
                                                <span v-if="isExpired(reservation.expires_at)" class="ml-1 font-medium text-red-600">
                                                    ({{ getDaysExpired(reservation.expires_at) }} days ago)
                                                </span>
                                                <span v-else-if="isExpiringSoon(reservation.expires_at)" class="ml-1 font-medium text-yellow-600">
                                                    ({{ getDaysUntilExpiry(reservation.expires_at) }} days left)
                                                </span>
                                            </div>
                                            
                                            <div v-if="reservation.queue_position" class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clipRule="evenodd"/>
                                                </svg>
                                                Queue position: #{{ reservation.queue_position }}
                                            </div>
                                            
                                            <div v-if="reservation.picked_up_at" class="flex items-center text-green-600">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd"/>
                                                </svg>
                                                Picked up: {{ formatDate(reservation.picked_up_at) }}
                                            </div>
                                        </div>

                                        <!-- Pickup Location -->
                                        <div v-if="reservation.pickup_location" class="text-sm text-gray-600 mb-2">
                                            <span class="font-medium">Pickup Location:</span> {{ reservation.pickup_location }}
                                        </div>

                                        <!-- Special Notes -->
                                        <div v-if="reservation.notes" class="text-sm text-gray-600 bg-gray-50 rounded p-2 mb-3">
                                            <span class="font-medium">Notes:</span> {{ reservation.notes }}
                                        </div>

                                        <!-- Status Messages -->
                                        <div v-if="reservation.status === 'ready'" class="bg-green-50 border border-green-200 rounded p-3 mb-3">
                                            <div class="flex items-start">
                                                <svg class="w-5 h-5 text-green-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd"/>
                                                </svg>
                                                <div>
                                                    <h4 class="text-sm font-medium text-green-800">Your book is ready for pickup!</h4>
                                                    <p class="text-sm text-green-700">Please collect it before {{ formatDate(reservation.expires_at) }}.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div v-if="isExpiringSoon(reservation.expires_at) && !isExpired(reservation.expires_at)" class="bg-yellow-50 border border-yellow-200 rounded p-3 mb-3">
                                            <div class="flex items-start">
                                                <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd"/>
                                                </svg>
                                                <div>
                                                    <h4 class="text-sm font-medium text-yellow-800">Reservation expiring soon</h4>
                                                    <p class="text-sm text-yellow-700">Only {{ getDaysUntilExpiry(reservation.expires_at) }} days left to pick up this book.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Badge and Actions -->
                                    <div class="flex flex-col items-end space-y-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              :class="{
                                                  'bg-blue-100 text-blue-800': reservation.status === 'pending',
                                                  'bg-green-100 text-green-800': reservation.status === 'ready',
                                                  'bg-red-100 text-red-800': reservation.status === 'expired',
                                                  'bg-gray-100 text-gray-800': reservation.status === 'cancelled' || reservation.status === 'fulfilled'
                                              }">
                                            {{ getStatusLabel(reservation.status) }}
                                        </span>

                                        <!-- Actions -->
                                        <div class="flex space-x-2">
                                            <button v-if="reservation.status === 'ready'" @click="confirmPickup(reservation.id)"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"/>
                                                </svg>
                                                Confirm Pickup
                                            </button>
                                            
                                            <button v-if="canExtendReservation(reservation)" @click="extendReservation(reservation.id)"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenoRule" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clipRule="evenodd"/>
                                                </svg>
                                                Extend
                                            </button>
                                            
                                            <button v-if="canCancelReservation(reservation)" @click="cancelReservation(reservation.id)"
                                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Cancel
                                            </button>
                                            
                                            <Link :href="`/books/${reservation.book.id}`"
                                                  class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                View Book
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">
                        {{ getEmptyStateTitle() }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ getEmptyStateMessage() }}
                    </p>
                    <div v-if="filter === 'active'" class="mt-6">
                        <Link href="/books" 
                              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clipRule="evenodd"/>
                            </svg>
                            Browse Books to Reserve
                        </Link>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="reservations.data && reservations.data.length > 0" class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-700">
                            Showing {{ reservations.from }} to {{ reservations.to }} of {{ reservations.total }} results
                        </p>
                        <div class="flex space-x-2">
                            <Link v-if="reservations.prev_page_url" 
                                  :href="reservations.prev_page_url" 
                                  class="px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </Link>
                            <Link v-if="reservations.next_page_url" 
                                  :href="reservations.next_page_url" 
                                  class="px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        <Footer :year="new Date().getFullYear()" />
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Header from '@/Components/AppHeader.vue'
import Footer from '@/Components/AppFooter.vue'

const props = defineProps({
    reservations: Object,
    summary: Object,
    filter: String,
    sortBy: String,
})

const filter = ref(props.filter || 'active')
const sortBy = ref(props.sortBy || 'created_at_desc')

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', { 
        year: 'numeric',
        month: 'short', 
        day: 'numeric' 
    })
}

const isExpired = (expiresAtString) => {
    return new Date(expiresAtString) < new Date()
}

const isExpiringSoon = (expiresAtString) => {
    const expiresAt = new Date(expiresAtString)
    const today = new Date()
    const diffTime = expiresAt.getTime() - today.getTime()
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays <= 3 && diffDays >= 0
}

const getDaysExpired = (expiresAtString) => {
    const expiresAt = new Date(expiresAtString)
    const today = new Date()
    const diffTime = today.getTime() - expiresAt.getTime()
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays
}

const getDaysUntilExpiry = (expiresAtString) => {
    const expiresAt = new Date(expiresAtString)
    const today = new Date()
    const diffTime = expiresAt.getTime() - today.getTime()
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays
}

const canExtendReservation = (reservation) => {
    return reservation.status === 'ready' && !isExpired(reservation.expires_at)
}

const canCancelReservation = (reservation) => {
    return ['pending', 'ready'].includes(reservation.status) && !isExpired(reservation.expires_at)
}

const getStatusLabel = (status) => {
    const statusLabels = {
        'pending': 'In Queue',
        'ready': 'Ready for Pickup',
        'expired': 'Expired',
        'cancelled': 'Cancelled',
        'fulfilled': 'Completed'
    }
    return statusLabels[status] || status.toUpperCase()
}

const setFilter = (newFilter) => {
    filter.value = newFilter
    applyFilters()
}

const applyFilters = () => {
    router.visit('/reservations', {
        data: { 
            filter: filter.value, 
            sort: sortBy.value 
        },
        preserveState: true,
        preserveScroll: true,
    })
}

const confirmPickup = (reservationId) => {
    if (confirm('Confirm that you have picked up this book?')) {
        router.post(`/reservations/${reservationId}/pickup`, {}, {
            onSuccess: () => {
                router.reload({ only: ['reservations', 'summary'] })
            },
            onError: (errors) => {
                alert(errors.message || 'Failed to confirm pickup')
            }
        })
    }
}

const extendReservation = (reservationId) => {
    if (confirm('Extend this reservation for 3 more days?')) {
        router.post(`/reservations/${reservationId}/extend`, {}, {
            onSuccess: () => {
                router.reload({ only: ['reservations', 'summary'] })
            },
            onError: (errors) => {
                alert(errors.message || 'Failed to extend reservation')
            }
        })
    }
}

const cancelReservation = (reservationId) => {
    if (confirm('Are you sure you want to cancel this reservation?')) {
        router.delete(`/reservations/${reservationId}`, {
            onSuccess: () => {
                router.reload({ only: ['reservations', 'summary'] })
            },
            onError: (errors) => {
                alert(errors.message || 'Failed to cancel reservation')
            }
        })
    }
}

const getEmptyStateTitle = () => {
    switch (filter.value) {
        case 'active':
            return 'No active reservations'
        case 'ready':
            return 'No books ready for pickup'
        case 'expired':
            return 'No expired reservations'
        case 'history':
            return 'No reservation history'
        default:
            return 'No reservations found'
    }
}

const getEmptyStateMessage = () => {
    switch (filter.value) {
        case 'active':
            return 'You have no active book reservations. Browse our collection to reserve books that interest you.'
        case 'ready':
            return 'No books are currently ready for pickup.'
        case 'expired':
            return 'You have no expired reservations.'
        case 'history':
            return 'Your reservation history will appear here once you start reserving books.'
        default:
            return 'Try adjusting your filters or reserve some books to get started.'
    }
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
</style>"