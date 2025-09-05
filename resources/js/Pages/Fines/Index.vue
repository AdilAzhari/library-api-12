<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Head title="My Fines"/>
        <Header/>
        
        <main class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">My Fines</h1>
                <p class="mt-2 text-gray-600">Manage your library fines and payment history</p>
            </div>

            <!-- Fine Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Outstanding</p>
                            <p class="text-2xl font-semibold text-red-600">${{ summary.total_outstanding.toFixed(2) }}</p>
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
                            <p class="text-sm font-medium text-gray-600">Total Paid</p>
                            <p class="text-2xl font-semibold text-green-600">${{ summary.total_paid.toFixed(2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clipRule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Unpaid Fines</p>
                            <p class="text-2xl font-semibold text-yellow-600">{{ summary.count_unpaid }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                                <path fillRule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clipRule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Payment Methods</p>
                            <p class="text-sm text-gray-900 mt-1">
                                {{ paymentMethods.join(', ').toUpperCase() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Fine Filters</h3>
                        <div class="flex space-x-4">
                            <select v-model="filters.status" @change="applyFilters" class="rounded-md border-gray-300 text-sm">
                                <option value="outstanding">Outstanding</option>
                                <option value="paid">Paid</option>
                                <option value="all">All Fines</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fines List -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ filters.status === 'outstanding' ? 'Outstanding Fines' : filters.status === 'paid' ? 'Paid Fines' : 'All Fines' }}
                    </h3>
                </div>
                
                <div v-if="fines.data && fines.data.length > 0" class="divide-y divide-gray-200">
                    <div v-for="fine in fines.data" :key="fine.id" class="px-6 py-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-12 bg-gray-100 rounded flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <h4 class="text-sm font-medium text-gray-900 truncate">
                                                {{ fine.borrow?.book?.title || 'Library Fine' }}
                                            </h4>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                  :class="{
                                                      'bg-red-100 text-red-800': fine.status === 'pending',
                                                      'bg-yellow-100 text-yellow-800': fine.status === 'partial',
                                                      'bg-green-100 text-green-800': fine.status === 'paid',
                                                      'bg-gray-100 text-gray-800': fine.status === 'waived'
                                                  }">
                                                {{ fine.status.replace('_', ' ').toUpperCase() }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600">
                                            {{ fine.borrow?.book?.author || '' }}
                                        </p>
                                        <div class="flex items-center text-xs text-gray-500 mt-1 space-x-4">
                                            <span>Reason: {{ fine.reason.replace('_', ' ') }}</span>
                                            <span>Created: {{ formatDate(fine.created_at) }}</span>
                                            <span v-if="fine.due_date">Due: {{ formatDate(fine.due_date) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900">
                                        ${{ fine.amount.toFixed(2) }}
                                    </p>
                                    <p v-if="fine.paid_amount > 0" class="text-sm text-green-600">
                                        Paid: ${{ fine.paid_amount.toFixed(2) }}
                                    </p>
                                    <p v-if="fine.status === 'partial'" class="text-sm text-yellow-600">
                                        Remaining: ${{ (fine.amount - fine.paid_amount).toFixed(2) }}
                                    </p>
                                </div>
                                
                                <div class="flex flex-col space-y-2">
                                    <Link v-if="fine.status !== 'paid' && fine.status !== 'waived'" 
                                          :href="`/fines/${fine.id}/pay`" 
                                          class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Pay Fine
                                    </Link>
                                    
                                    <Link :href="`/fines/${fine.id}`" 
                                          class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        View Details
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Empty State -->
                <div v-else class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No fines found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ filters.status === 'outstanding' ? "You don't have any outstanding fines." : "No fines match your current filter." }}
                    </p>
                </div>
                
                <!-- Pagination -->
                <div v-if="fines.data && fines.data.length > 0" class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-700">
                            Showing {{ fines.from }} to {{ fines.to }} of {{ fines.total }} results
                        </p>
                        <div class="flex space-x-2">
                            <Link v-if="fines.prev_page_url" 
                                  :href="fines.prev_page_url" 
                                  class="px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </Link>
                            <Link v-if="fines.next_page_url" 
                                  :href="fines.next_page_url" 
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
    fines: Object,
    summary: Object,
    paymentMethods: Array,
    filters: Object,
})

const filters = ref({
    status: props.filters.status || 'outstanding'
})

const applyFilters = () => {
    router.visit('/fines', {
        data: { status: filters.value.status },
        preserveState: true,
        preserveScroll: true,
    })
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', { 
        year: 'numeric',
        month: 'short', 
        day: 'numeric' 
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
</style>