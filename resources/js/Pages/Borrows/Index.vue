<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Head title="My Borrowed Books"/>
        <Header/>
        
        <main class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">My Borrowed Books</h1>
                <p class="mt-2 text-gray-600">Manage your current and past book borrowings</p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Currently Borrowed</p>
                            <p class="text-2xl font-semibold text-blue-600">{{ summary.active_borrows }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clipRule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Overdue Books</p>
                            <p class="text-2xl font-semibold text-red-600">{{ summary.overdue_borrows }}</p>
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
                            <p class="text-sm font-medium text-gray-600">Due Soon</p>
                            <p class="text-2xl font-semibold text-yellow-600">{{ summary.due_soon }}</p>
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
                            <p class="text-sm font-medium text-gray-600">Total Returned</p>
                            <p class="text-2xl font-semibold text-green-600">{{ summary.total_returned }}</p>
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
                                Active Borrows ({{ summary.active_borrows }})
                            </button>
                            <button @click="setFilter('overdue')" 
                                    :class="filter === 'overdue' ? 'text-red-600 border-red-600' : 'text-gray-500 border-transparent'"
                                    class="border-b-2 py-2 px-1 text-sm font-medium hover:text-red-600">
                                Overdue ({{ summary.overdue_borrows }})
                            </button>
                            <button @click="setFilter('history')" 
                                    :class="filter === 'history' ? 'text-green-600 border-green-600' : 'text-gray-500 border-transparent'"
                                    class="border-b-2 py-2 px-1 text-sm font-medium hover:text-green-600">
                                History
                            </button>
                        </div>
                        <div class="flex space-x-2">
                            <select v-model="sortBy" @change="applyFilters" class="rounded-md border-gray-300 text-sm">
                                <option value="borrowed_at_desc">Recently Borrowed</option>
                                <option value="borrowed_at_asc">Oldest First</option>
                                <option value="due_date_asc">Due Date (Earliest)</option>
                                <option value="due_date_desc">Due Date (Latest)</option>
                                <option value="title_asc">Book Title A-Z</option>
                                <option value="title_desc">Book Title Z-A</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Borrows List -->
            <div class="bg-white rounded-lg shadow">
                <div v-if="borrows.data && borrows.data.length > 0" class="divide-y divide-gray-200">
                    <div v-for="borrow in borrows.data" :key="borrow.id" class="px-6 py-6">
                        <div class="flex items-start space-x-4">
                            <!-- Book Cover -->
                            <div class="flex-shrink-0 w-16 h-20 bg-gray-100 rounded flex items-center justify-center">
                                <img v-if="borrow.book.cover_image_url" 
                                     :src="borrow.book.cover_image_url" 
                                     :alt="borrow.book.title"
                                     class="w-full h-full object-cover rounded">
                                <svg v-else class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>

                            <!-- Borrow Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-1">
                                            {{ borrow.book.title }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-2">
                                            by {{ borrow.book.author }}
                                        </p>
                                        
                                        <!-- Status and Dates -->
                                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clipRule="evenodd"/>
                                                </svg>
                                                Borrowed: {{ formatDate(borrow.borrowed_at) }}
                                            </div>
                                            
                                            <div class="flex items-center" :class="{ 
                                                'text-red-600': isOverdue(borrow.due_date), 
                                                'text-yellow-600': isDueSoon(borrow.due_date) && !isOverdue(borrow.due_date)
                                            }">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clipRule="evenodd"/>
                                                </svg>
                                                Due: {{ formatDate(borrow.due_date) }}
                                                <span v-if="isOverdue(borrow.due_date)" class="ml-1 font-medium text-red-600">
                                                    ({{ getDaysOverdue(borrow.due_date) }} days overdue)
                                                </span>
                                                <span v-else-if="isDueSoon(borrow.due_date)" class="ml-1 font-medium text-yellow-600">
                                                    (Due in {{ getDaysUntilDue(borrow.due_date) }} days)
                                                </span>
                                            </div>
                                            
                                            <div v-if="borrow.returned_at" class="flex items-center text-green-600">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd"/>
                                                </svg>
                                                Returned: {{ formatDate(borrow.returned_at) }}
                                            </div>
                                        </div>

                                        <!-- Renewal Info -->
                                        <div v-if="borrow.renewals_count > 0" class="mt-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Renewed {{ borrow.renewals_count }} time{{ borrow.renewals_count > 1 ? 's' : '' }}
                                            </span>
                                        </div>

                                        <!-- Fine Info -->
                                        <div v-if="borrow.fine_amount && borrow.fine_amount > 0" class="mt-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Fine: ${{ borrow.fine_amount.toFixed(2) }}
                                                <span v-if="borrow.fine_status === 'paid'" class="ml-1">(Paid)</span>
                                                <span v-else-if="borrow.fine_status === 'pending'" class="ml-1">(Pending)</span>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Status Badge -->
                                    <div class="flex flex-col items-end space-y-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              :class="{
                                                  'bg-green-100 text-green-800': borrow.status === 'active' && !isOverdue(borrow.due_date),
                                                  'bg-red-100 text-red-800': borrow.status === 'active' && isOverdue(borrow.due_date),
                                                  'bg-gray-100 text-gray-800': borrow.status === 'returned'
                                              }">
                                            {{ borrow.status === 'active' && isOverdue(borrow.due_date) ? 'OVERDUE' : borrow.status.toUpperCase() }}
                                        </span>

                                        <!-- Actions -->
                                        <div v-if="borrow.status === 'active'" class="flex space-x-2">
                                            <button v-if="canRenew(borrow)" @click="renewBorrow(borrow.id)"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clipRule="evenodd"/>
                                                </svg>
                                                Renew
                                            </button>
                                            
                                            <Link :href="`/borrows/${borrow.id}/return`"
                                                  class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"/>
                                                </svg>
                                                Return
                                            </Link>
                                        </div>
                                        
                                        <div v-else class="flex space-x-2">
                                            <button v-if="borrow.fine_amount > 0 && borrow.fine_status !== 'paid'" 
                                                    @click="payFine(borrow.id)"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                Pay Fine
                                            </button>
                                            
                                            <Link :href="`/books/${borrow.book.id}`"
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
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
                            Browse Books
                        </Link>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="borrows.data && borrows.data.length > 0" class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-700">
                            Showing {{ borrows.from }} to {{ borrows.to }} of {{ borrows.total }} results
                        </p>
                        <div class="flex space-x-2">
                            <Link v-if="borrows.prev_page_url" 
                                  :href="borrows.prev_page_url" 
                                  class="px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </Link>
                            <Link v-if="borrows.next_page_url" 
                                  :href="borrows.next_page_url" 
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
    borrows: Object,
    summary: Object,
    filter: String,
    sortBy: String,
})

const filter = ref(props.filter || 'active')
const sortBy = ref(props.sortBy || 'borrowed_at_desc')

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', { 
        year: 'numeric',
        month: 'short', 
        day: 'numeric' 
    })
}

const isOverdue = (dueDateString) => {
    return new Date(dueDateString) < new Date()
}

const isDueSoon = (dueDateString) => {
    const dueDate = new Date(dueDateString)
    const today = new Date()
    const diffTime = dueDate.getTime() - today.getTime()
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays <= 3 && diffDays >= 0
}

const getDaysOverdue = (dueDateString) => {
    const dueDate = new Date(dueDateString)
    const today = new Date()
    const diffTime = today.getTime() - dueDate.getTime()
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays
}

const getDaysUntilDue = (dueDateString) => {
    const dueDate = new Date(dueDateString)
    const today = new Date()
    const diffTime = dueDate.getTime() - today.getTime()
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays
}

const canRenew = (borrow) => {
    // Check if book can be renewed (not overdue, under renewal limit)
    return !isOverdue(borrow.due_date) && (borrow.renewals_count || 0) < 3
}

const setFilter = (newFilter) => {
    filter.value = newFilter
    applyFilters()
}

const applyFilters = () => {
    router.visit('/borrows', {
        data: { 
            filter: filter.value, 
            sort: sortBy.value 
        },
        preserveState: true,
        preserveScroll: true,
    })
}

const renewBorrow = (borrowId) => {
    if (confirm('Are you sure you want to renew this book?')) {
        router.post(`/borrows/${borrowId}/renew`, {}, {
            onSuccess: () => {
                router.reload({ only: ['borrows', 'summary'] })
            },
            onError: (errors) => {
                alert(errors.message || 'Failed to renew book')
            }
        })
    }
}

const payFine = (borrowId) => {
    router.visit(`/borrows/${borrowId}/fine/pay`)
}

const getEmptyStateTitle = () => {
    switch (filter.value) {
        case 'active':
            return 'No active borrows'
        case 'overdue':
            return 'No overdue books'
        case 'history':
            return 'No borrowing history'
        default:
            return 'No borrows found'
    }
}

const getEmptyStateMessage = () => {
    switch (filter.value) {
        case 'active':
            return 'You have no books currently borrowed. Browse our collection to find your next read.'
        case 'overdue':
            return 'Great! You have no overdue books. Keep up the good work.'
        case 'history':
            return 'Your borrowing history will appear here once you start borrowing books.'
        default:
            return 'Try adjusting your filters or borrow some books to get started.'
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
</style>