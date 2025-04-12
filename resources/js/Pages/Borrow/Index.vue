<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Header/>

        <main class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- Search and Filters -->
            <div class="mb-6 bg-white rounded-lg shadow-sm p-4 border border-[#e8e3d5]">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Search Input -->
                    <div class="flex-grow">
                        <div class="relative">
                            <input
                                type="text"
                                placeholder="Search by book title, author or user..."
                                class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300"
                                v-model="searchQuery"
                            />
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="flex-shrink-0">
                        <select
                            v-model="activeStatus"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-300 focus:border-amber-300"
                        >
                            <option :value="null">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="overdue">Overdue</option>
                            <option value="returned">Returned</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <div v-if="$page.props.flash.success" class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash.error" class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg">
                {{ $page.props.flash.error }}
            </div>

            <!-- Sort and Count -->
            <div class="mb-4 flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    Showing {{ filteredBorrowings.length }} borrowings
                </div>
                <div class="flex items-center space-x-2">
                    <label for="sort" class="text-sm font-medium text-gray-700">Sort by:</label>
                    <select
                        id="sort"
                        v-model="sortField"
                        class="rounded-md border-gray-300 py-1 pl-2 pr-8 text-sm focus:border-amber-300 focus:outline-none focus:ring-amber-300"
                    >
                        <option value="due_date">Due Date</option>
                        <option value="borrowed_at">Borrow Date</option>
                        <option value="book.title">Book Title</option>
                    </select>
                    <button
                        @click="sortOrder = sortOrder === 'asc' ? 'desc' : 'asc'"
                        class="p-1 rounded-md text-gray-500 hover:bg-gray-100"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  :d="sortOrder === 'asc' ? 'M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4' : 'M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12'"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Borrowings Grid -->
            <div v-if="filteredBorrowings.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="borrowing in sortedBorrowings"
                    :key="borrowing.id"
                    class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 border border-[#e8e3d5]"
                >
                    <!-- Status Indicator -->
                    <div class="h-2" :class="{
                        'bg-green-500': borrowing.returned_at,
                        'bg-blue-500': !borrowing.returned_at && !borrowing.is_overdue,
                        'bg-red-500': borrowing.is_overdue
                    }"></div>

                    <div class="p-5">
                        <!-- Book Info -->
                        <div class="flex items-start gap-4 mb-4">
                            <div
                                class="flex-shrink-0 w-16 h-24 bg-gray-200 rounded-md overflow-hidden border border-[#e8e3d5]">
                                <img
                                    :src="borrowing.book.cover_image_url || '/images/book-placeholder.png'"
                                    :alt="borrowing.book.title"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                            <div>
                                <h3 class="text-lg font-serif font-bold text-gray-900">{{ borrowing.book.title }}</h3>
                                <p class="text-sm text-[#8b5a2b]">{{ borrowing.book.author }}</p>

                                <!-- Status Badge -->
                                <span class="inline-block mt-2 px-2 py-1 text-xs font-medium rounded-full"
                                      :class="{
                                        'bg-green-100 text-green-800': borrowing.returned_at,
                                        'bg-blue-100 text-blue-800': !borrowing.returned_at && !borrowing.is_overdue,
                                        'bg-red-100 text-red-800': borrowing.is_overdue
                                    }"
                                >
                                    <span v-if="borrowing.returned_at">Returned</span>
                                    <span v-else-if="borrowing.is_overdue">Overdue</span>
                                    <span v-else>Active</span>
                                </span>
                            </div>
                        </div>

                        <!-- Borrowing Details -->
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Borrowed by:</span>
                                <span class="font-medium">{{ borrowing.user.name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Borrowed on:</span>
                                <span class="font-medium">{{ formatDate(borrowing.borrowed_at) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Due date:</span>
                                <span class="font-medium" :class="{
                                    'text-red-600': borrowing.is_overdue && !borrowing.returned_at,
                                    'text-green-600': !borrowing.is_overdue
                                }">
                                    {{ formatDate(borrowing.due_date) }}
                                </span>
                            </div>
                            <div v-if="borrowing.returned_at" class="flex justify-between">
                                <span>Returned on:</span>
                                <span class="font-medium text-green-600">{{ formatDate(borrowing.returned_at) }}</span>
                            </div>
                            <div v-if="borrowing.late_fee > 0" class="flex justify-between">
                                <span>Late fee:</span>
                                <span class="font-medium text-red-600">${{
                                        Number(borrowing.late_fee).toFixed(2)
                                    }}</span>
                            </div>
                            <div v-if="borrowing.renewal_count > 0" class="flex justify-between">
                                <span>Renewals:</span>
                                <span class="font-medium">{{ borrowing.renewal_count }}</span>
                            </div>
                            <div v-if="borrowing.notes" class="pt-2 border-t border-gray-100">
                                <p class="text-xs italic">Notes: {{ borrowing.notes }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-center">
                            <Link
                                :href="`/borrows/${borrowing.id}`"
                                class="text-sm text-[#2c3e50] hover:text-amber-600 font-medium flex items-center"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Details
                            </Link>

                            <div class="flex space-x-3">
                                <button
                                    v-if="!borrowing.returned_at && canRenew(borrowing)"
                                    @click="renewBorrowing(borrowing.id)"
                                    class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Renew
                                </button>

                                <button
                                    v-if="!borrowing.returned_at"
                                    @click="returnBorrowing(borrowing.id)"
                                    class="text-sm text-green-600 hover:text-green-800 font-medium flex items-center"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Return
                                </button>

                                <button
                                    v-if="isAdmin"
                                    @click="deleteBorrowing(borrowing.id)"
                                    class="text-sm text-red-600 hover:text-red-800 font-medium flex items-center"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16 bg-white rounded-xl shadow-sm border border-[#e8e3d5]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 font-serif">
                    No borrowings found
                </h3>
                <p class="mt-1 text-gray-500 max-w-md mx-auto">
                    {{
                        activeStatus === null ? 'You have no borrowing records yet.' : `You have no ${activeStatus} borrowings.`
                    }}
                </p>
                <div class="mt-6">
                    <Link href="/books"
                          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700">
                        Browse Books
                    </Link>
                </div>
            </div>
        </main>

        <Footer/>
    </div>
</template>

<script setup>
import {computed, ref} from 'vue';
import {Link, router} from '@inertiajs/vue3';
import Header from '@/Components/AppHeader.vue';
import Footer from '@/Components/AppFooter.vue';

const props = defineProps({
    borrowings: {
        type: Array,
        default: () => []
    },
    isAdmin: {
        type: Boolean,
        default: false
    },
    maxRenewals: {
        type: Number,
        default: 2
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const searchQuery = ref(props.filters.search || '');
const activeStatus = ref(props.filters.status || null);
const sortField = ref(props.filters.sort_by || 'due_date');
const sortOrder = ref(props.filters.sort_order || 'asc');

const filteredBorrowings = computed(() => {
    let result = props.borrowings;

    // Apply status filter
    if (activeStatus.value) {
        result = result.filter(borrowing => {
            switch (activeStatus.value) {
                case 'active':
                    return !borrowing.returned_at && !borrowing.is_overdue;
                case 'overdue':
                    return borrowing.is_overdue;
                case 'returned':
                    return borrowing.returned_at;
                default:
                    return true;
            }
        });
    }

    // Apply search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(borrowing => {
            return (
                borrowing.book.title.toLowerCase().includes(query) ||
                borrowing.book.author.toLowerCase().includes(query) ||
                borrowing.user.name.toLowerCase().includes(query)
            );
        });
    }

    return result;
});

const sortedBorrowings = computed(() => {
    const field = sortField.value;
    const order = sortOrder.value === 'asc' ? 1 : -1;

    return [...filteredBorrowings.value].sort((a, b) => {
        // Handle nested properties
        let aValue, bValue;

        if (field.includes('.')) {
            const [obj, prop] = field.split('.');
            aValue = a[obj][prop];
            bValue = b[obj][prop];
        } else {
            aValue = a[field];
            bValue = b[field];
        }

        // Compare dates if the field is a date
        if (['borrowed_at', 'due_date', 'returned_at'].includes(field)) {
            aValue = new Date(aValue).getTime();
            bValue = new Date(bValue).getTime();
        }

        // Compare strings
        if (typeof aValue === 'string' && typeof bValue === 'string') {
            return aValue.localeCompare(bValue) * order;
        }

        // Compare numbers/dates
        return (aValue - bValue) * order;
    });
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const canRenew = (borrowing) => {
    return !borrowing.returned_at &&
        borrowing.renewal_count < props.maxRenewals &&
        !borrowing.is_overdue;
};

const renewBorrowing = (id) => {
    router.post(`/borrows/${id}/renew`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Success handled by flash message
        }
    });
};

const returnBorrowing = (id) => {
    router.post(`/borrows/${id}/return`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Success handled by flash message
        }
    });
};

const deleteBorrowing = (id) => {
    if (confirm('Are you sure you want to delete this borrowing record?')) {
        router.delete(`/borrows/${id}`, {
            preserveScroll: true
        });
    }
};
</script>

<style scoped>
button, a {
    transition: all 0.2s ease;
}
</style>
