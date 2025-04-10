<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Header/>

        <main class="container mx-auto px-4 py-8 max-w-4xl">
            <!-- Back Button -->
            <div class="mb-6">
                <Link href="/borrows" class="flex items-center text-[#2c3e50] hover:text-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Borrowings
                </Link>
            </div>

            <!-- Flash Messages -->
            <div v-if="$page.props.flash.success" class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash.error" class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg">
                {{ $page.props.flash.error }}
            </div>

            <!-- Borrowing Details Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-[#e8e3d5]">
                <!-- Status Indicator -->
                <div class="h-2" :class="{
                    'bg-green-500': borrow.returned_at,
                    'bg-blue-500': !borrow.returned_at && !borrow.is_overdue,
                    'bg-red-500': borrow.is_overdue
                }"></div>

                <div class="p-6">
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Book Cover -->
                        <div class="w-full md:w-1/3">
                            <div class="bg-gray-100 rounded-lg overflow-hidden border border-[#e8e3d5]">
                                <img
                                    :src="borrow.book.cover_image_url || '/images/book-placeholder.png'"
                                    :alt="borrow.book.title"
                                    class="w-full h-auto object-cover"
                                />
                            </div>
                        </div>

                        <!-- Borrowing Details -->
                        <div class="w-full md:w-2/3">
                            <h1 class="text-2xl font-serif font-bold text-gray-900 mb-2">{{ borrow.book.title }}</h1>
                            <p class="text-lg text-[#8b5a2b] mb-4">{{ borrow.book.author }}</p>

                            <!-- Status Badge -->
                            <div class="mb-6">
                                <span class="inline-block px-3 py-1 text-sm font-medium rounded-full"
                                      :class="{
                                        'bg-green-100 text-green-800': borrow.returned_at,
                                        'bg-blue-100 text-blue-800': !borrow.returned_at && !borrow.is_overdue,
                                        'bg-red-100 text-red-800': borrow.is_overdue
                                    }">
                                    <span v-if="borrow.returned_at">Returned</span>
                                    <span v-else-if="borrow.is_overdue">Overdue</span>
                                    <span v-else>Active</span>
                                </span>
                            </div>

                            <!-- Details Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Borrowed By</h3>
                                    <p class="mt-1 text-sm text-gray-900">{{ borrow.user.name }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Borrow Date</h3>
                                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(borrow.borrowed_at) }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Due Date</h3>
                                    <p class="mt-1 text-sm" :class="{
                                        'text-red-600': borrow.is_overdue && !borrow.returned_at,
                                        'text-gray-900': !borrow.is_overdue
                                    }">
                                        {{ formatDate(borrow.due_date) }}
                                    </p>
                                </div>
                                <div v-if="borrow.returned_at">
                                    <h3 class="text-sm font-medium text-gray-500">Return Date</h3>
                                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(borrow.returned_at) }}</p>
                                </div>
                                <div v-if="borrow.late_fee > 0">
                                    <h3 class="text-sm font-medium text-gray-500">Late Fee</h3>
                                    <p class="mt-1 text-sm text-red-600">${{ borrow.late_fee.toFixed(2) }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Renewals</h3>
                                    <p class="mt-1 text-sm text-gray-900">{{ borrow.renewal_count }}</p>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div v-if="borrow.notes" class="mb-6 p-4 bg-gray-50 rounded-lg">
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Notes</h3>
                                <p class="text-sm text-gray-700">{{ borrow.notes }}</p>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
                                <button
                                    v-if="!borrow.returned_at && canRenew"
                                    @click="renewBorrowing"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Renew
                                </button>

                                <button
                                    v-if="!borrow.returned_at"
                                    @click="returnBorrowing"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Return Book
                                </button>

                                <Link
                                    :href="`/books/${borrow.book.id}`"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    View Book
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Borrowing History for this Book -->
            <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden border border-[#e8e3d5]">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-serif font-medium text-gray-900">Borrowing History for This Book</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    <div v-for="history in borrowHistory.data" :key="history.id" class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ history.user.name }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ formatDate(history.borrowed_at) }} -
                                    <span v-if="history.returned_at">{{ formatDate(history.returned_at) }}</span>
                                    <span v-else>Not returned yet</span>
                                </p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  :class="{
                                    'bg-green-100 text-green-800': history.returned_at,
                                    'bg-blue-100 text-blue-800': !history.returned_at && !history.is_overdue,
                                    'bg-red-100 text-red-800': history.is_overdue
                                }">
                                {{ history.returned_at ? 'Returned' : (history.is_overdue ? 'Overdue' : 'Active') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div v-if="borrowHistory.data.length === 0" class="px-6 py-4 text-center text-gray-500">
                    No previous borrowings found for this book
                </div>
            </div>
        </main>

        <Footer/>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Header from '@/Components/AppHeader.vue';
import Footer from '@/Components/AppFooter.vue';

const props = defineProps({
    borrow: {
        type: Object,
        required: true
    },
    borrowHistory: {
        type: Object,
        required: true
    },
    canRenew: {
        type: Boolean,
        default: false
    }
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const renewBorrowing = () => {
    router.post(`/borrows/${props.borrow.id}/renew`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Success handled by flash message
        }
    });
};

const returnBorrowing = () => {
    router.post(`/borrows/${props.borrow.id}/return`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Success handled by flash message
        }
    });
};
</script>
