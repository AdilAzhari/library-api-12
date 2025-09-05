<template>
    <AuthenticatedLayout>
        <Head title="Fine Details"/>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <Link href="/fines" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    ← Back to Fines
                </Link>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">Fine Details</h1>
                <div class="flex items-center mt-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mr-3"
                          :class="{
                              'bg-red-100 text-red-800': fine.status === 'pending',
                              'bg-yellow-100 text-yellow-800': fine.status === 'partial',
                              'bg-green-100 text-green-800': fine.status === 'paid',
                              'bg-gray-100 text-gray-800': fine.status === 'waived'
                          }">
                        {{ fine.status.replace('_', ' ').toUpperCase() }}
                    </span>
                    <span class="text-2xl font-bold text-gray-900">${{ fine.amount.toFixed(2) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Fine Information -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Fine Information</h3>
                    </div>
                    <div class="px-6 py-6">
                        <!-- Book Information if available -->
                        <div v-if="fine.borrow?.book" class="flex items-start space-x-4 mb-6 pb-6 border-b">
                            <div class="flex-shrink-0 w-16 h-20 bg-gray-100 rounded flex items-center justify-center">
                                <img v-if="fine.borrow.book.cover_image_url" 
                                     :src="fine.borrow.book.cover_image_url" 
                                     :alt="fine.borrow.book.title"
                                     class="w-full h-full object-cover rounded">
                                <svg v-else class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-medium text-gray-900">{{ fine.borrow.book.title }}</h4>
                                <p class="text-sm text-gray-600 mt-1">{{ fine.borrow.book.author }}</p>
                                <p class="text-xs text-gray-500 mt-2">ISBN: {{ fine.borrow.book.isbn || 'N/A' }}</p>
                                <Link :href="`/books/${fine.borrow.book.id}`" 
                                      class="text-sm text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                    View Book Details →
                                </Link>
                            </div>
                        </div>

                        <!-- Fine Details -->
                        <div class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Fine ID</dt>
                                <dd class="mt-1 text-sm text-gray-900">#{{ fine.id }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-600">Reason</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ fine.reason.replace('_', ' ') }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-600">Created Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(fine.created_at) }}</dd>
                            </div>

                            <div v-if="fine.due_date">
                                <dt class="text-sm font-medium text-gray-600">Due Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 flex items-center">
                                    {{ formatDate(fine.due_date) }}
                                    <span v-if="isOverdue" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ daysPastDue }} days overdue
                                    </span>
                                </dd>
                            </div>

                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center mb-2">
                                    <dt class="text-sm font-medium text-gray-600">Total Amount</dt>
                                    <dd class="text-lg font-bold text-gray-900">${{ fine.amount.toFixed(2) }}</dd>
                                </div>

                                <div v-if="fine.paid_amount > 0" class="flex justify-between items-center mb-2">
                                    <dt class="text-sm font-medium text-gray-600">Amount Paid</dt>
                                    <dd class="text-lg font-semibold text-green-600">${{ fine.paid_amount.toFixed(2) }}</dd>
                                </div>

                                <div v-if="fine.status === 'partial'" class="flex justify-between items-center">
                                    <dt class="text-sm font-medium text-gray-600">Remaining Balance</dt>
                                    <dd class="text-lg font-bold text-red-600">${{ (fine.amount - fine.paid_amount).toFixed(2) }}</dd>
                                </div>
                            </div>

                            <!-- Payment Button -->
                            <div v-if="fine.status !== 'paid' && fine.status !== 'waived'" class="pt-4">
                                <Link :href="`/fines/${fine.id}/pay`" 
                                      class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Pay Fine
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Payment History</h3>
                    </div>
                    <div class="px-6 py-6">
                        <div v-if="paymentHistory && paymentHistory.length > 0" class="space-y-4">
                            <div v-for="payment in paymentHistory" :key="payment.id" 
                                 class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            ${{ payment.amount.toFixed(2) }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ payment.payment_method.replace('_', ' ').toUpperCase() }}
                                            {{ payment.reference_number ? `• ${payment.reference_number}` : '' }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ formatDate(payment.processed_at || payment.created_at) }}
                                        </p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ payment.status || 'Completed' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Empty State -->
                        <div v-else class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No payments yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Payment history will appear here once payments are made.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Borrowing History (if available) -->
            <div v-if="fine.borrow" class="mt-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Related Borrowing</h3>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Borrowed Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(fine.borrow.borrowed_at) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Due Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(fine.borrow.due_date) }}</dd>
                        </div>
                        <div v-if="fine.borrow.returned_at">
                            <dt class="text-sm font-medium text-gray-600">Returned Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(fine.borrow.returned_at) }}</dd>
                        </div>
                    </div>
                    
                    <div v-if="!fine.borrow.returned_at" class="mt-4 p-4 bg-yellow-50 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Book Still Outstanding</h3>
                                <p class="text-sm text-yellow-700 mt-1">
                                    This book has not been returned yet. Please return it to avoid additional fines.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    fine: Object,
    paymentHistory: Array,
})

const isOverdue = computed(() => {
    if (!props.fine.due_date) return false
    return new Date(props.fine.due_date) < new Date()
})

const daysPastDue = computed(() => {
    if (!isOverdue.value) return 0
    const dueDate = new Date(props.fine.due_date)
    const today = new Date()
    const timeDiff = today.getTime() - dueDate.getTime()
    return Math.ceil(timeDiff / (1000 * 3600 * 24))
})

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', { 
        year: 'numeric',
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>