<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <!-- Header -->
        <header class="bg-[#2c3e50] text-white shadow-lg">
            <div class="container mx-auto px-4 py-6 flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <h1 class="text-2xl font-serif font-bold">My Reservations</h1>
                </div>
                <nav class="hidden md:flex space-x-6">
                    <Link href="/books" class="hover:text-amber-300 transition">Browse Books</Link>
                    <Link href="/borrowings" class="hover:text-amber-300 transition">My Loans</Link>
                    <Link href="/reservations" class="bg-amber-500 hover:bg-amber-600 px-4 py-2 rounded transition">Reservations</Link>
                </nav>
            </div>
        </header>

        <main class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- Hero Section -->
            <div class="mb-12 bg-[#e8e3d5] rounded-xl p-8">
                <h2 class="text-3xl font-serif font-bold text-[#2c3e50] mb-3">Manage Your Reservations</h2>
                <p class="text-lg text-gray-700 mb-6">Track your current and past book reservations</p>

                <!-- Status tabs -->
                <div class="border-b border-[#d4c9a8]">
                    <nav class="flex space-x-8">
                        <button
                            @click="activeStatus = null"
                            :class="{
                                'border-b-2 border-amber-500 text-amber-600': activeStatus === null,
                                'border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeStatus !== null
                            }"
                            class="px-1 py-4 text-sm font-medium font-serif"
                        >
                            All Reservations
                        </button>
                        <button
                            @click="activeStatus = 'active'"
                            :class="{
                                'border-b-2 border-amber-500 text-amber-600': activeStatus === 'active',
                                'border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeStatus !== 'active'
                            }"
                            class="px-1 py-4 text-sm font-medium font-serif"
                        >
                            Active
                        </button>
                        <button
                            @click="activeStatus = 'fulfilled'"
                            :class="{
                                'border-b-2 border-amber-500 text-amber-600': activeStatus === 'fulfilled',
                                'border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeStatus !== 'fulfilled'
                            }"
                            class="px-1 py-4 text-sm font-medium font-serif"
                        >
                            Fulfilled
                        </button>
                        <button
                            @click="activeStatus = 'expired'"
                            :class="{
                                'border-b-2 border-amber-500 text-amber-600': activeStatus === 'expired',
                                'border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeStatus !== 'expired'
                            }"
                            class="px-1 py-4 text-sm font-medium font-serif"
                        >
                            Expired
                        </button>
                        <button
                            @click="activeStatus = 'canceled'"
                            :class="{
                                'border-b-2 border-amber-500 text-amber-600': activeStatus === 'canceled',
                                'border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeStatus !== 'canceled'
                            }"
                            class="px-1 py-4 text-sm font-medium font-serif"
                        >
                            Canceled
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Empty state -->
            <div v-if="filteredReservations.length === 0" class="text-center py-16 bg-white rounded-xl shadow-sm border border-[#e8e3d5]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 font-serif">
                    {{ activeStatus === null ? 'No reservations yet' : `No ${activeStatus} reservations` }}
                </h3>
                <p class="mt-1 text-gray-500 max-w-md mx-auto">
                    {{ activeStatus === null
                    ? 'You haven\'t made any book reservations. Browse our collection and reserve books.'
                    : `You don't have any ${activeStatus} reservations at this time.`
                    }}
                </p>
                <div class="mt-6">
                    <Link href="/books" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700">
                        Browse Books
                    </Link>
                </div>
            </div>

            <!-- Reservations grid -->
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div
                    v-for="reservation in filteredReservations"
                    :key="reservation.id"
                    class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col border border-[#e8e3d5]"
                >
                    <!-- Status indicator -->
                    <div
                        class="h-2"
                        :class="{
                            'bg-green-500': reservation.is_fulfilled,
                            'bg-blue-500': reservation.is_active && !reservation.is_expired,
                            'bg-amber-500': reservation.is_expired && !reservation.is_canceled,
                            'bg-gray-500': reservation.is_canceled
                        }"
                    ></div>

                    <div class="p-4 flex gap-4">
                        <div class="flex-shrink-0 w-16 h-24 bg-gray-200 rounded-md overflow-hidden border border-[#e8e3d5]">
                            <img
                                :src="reservation.book.cover_image_url || placeholderImage"
                                alt="Book cover"
                                class="w-full h-full object-cover"
                                @error="onImageError"
                            />
                        </div>
                        <div class="flex-grow">
                            <!-- Status badge -->
                            <div class="mb-2">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-green-100 text-green-800': reservation.is_fulfilled,
                                        'bg-blue-100 text-blue-800': reservation.is_active && !reservation.is_expired,
                                        'bg-amber-100 text-amber-800': reservation.is_expired && !reservation.is_canceled,
                                        'bg-gray-100 text-gray-800': reservation.is_canceled
                                    }"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-3 w-3 mr-1"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            v-if="reservation.is_fulfilled"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 13l4 4L19 7"
                                        />
                                        <path
                                            v-else-if="reservation.is_active"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                        <path
                                            v-else-if="reservation.is_expired"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                        <path
                                            v-else-if="reservation.is_canceled"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                    {{ getStatusText(reservation) }}
                                </span>
                            </div>

                            <h2 class="text-lg font-medium text-gray-900 line-clamp-2 font-serif">{{ reservation.book.title }}</h2>
                            <p class="text-sm text-[#8b5a2b]">{{ reservation.book.author }}</p>

                            <!-- Reservation dates -->
                            <div class="mt-3 grid grid-cols-2 gap-y-1 text-xs text-gray-500">
                                <div>Reserved:</div>
                                <div class="text-right font-medium">{{ formatDate(reservation.reserved_at) }}</div>

                                <div>Expires:</div>
                                <div class="text-right font-medium" :class="{
                                    'text-red-600': reservation.is_expired && !reservation.is_fulfilled,
                                    'text-green-600': !reservation.is_expired
                                }">
                                    {{ formatDate(reservation.expires_at) }}
                                </div>

                                <div v-if="reservation.fulfilled_at">Fulfilled:</div>
                                <div v-if="reservation.fulfilled_at" class="text-right font-medium text-green-600">
                                    {{ formatDate(reservation.fulfilled_at) }}
                                </div>

                                <div v-if="reservation.canceled_at">Canceled:</div>
                                <div v-if="reservation.canceled_at" class="text-right font-medium text-gray-600">
                                    {{ formatDate(reservation.canceled_at) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="border-t border-gray-100 p-3 flex justify-between items-center bg-[#f9f7f2]">
                        <Link
                            :href="`/books/${reservation.book.id}`"
                            class="text-sm text-[#2c3e50] hover:text-amber-600 font-medium flex items-center"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            View Book
                        </Link>

                        <div class="flex space-x-2">
                            <button
                                v-if="reservation.is_active"
                                @click="cancelReservation(reservation.id)"
                                class="text-sm text-red-600 hover:text-red-800 font-medium flex items-center"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancel
                            </button>

                            <button
                                v-else-if="reservation.is_fulfilled"
                                @click="borrowBook(reservation.book.id)"
                                class="text-sm text-green-600 hover:text-green-800 font-medium flex items-center"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                Borrow Again
                            </button>

                            <span
                                v-else
                                class="text-sm text-gray-500 flex items-center"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ reservation.is_canceled ? 'Canceled' : 'Expired' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-[#2c3e50] text-white py-8 mt-12">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-6 md:mb-0">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <h2 class="text-xl font-serif font-bold">Academia Inspired</h2>
                        </div>
                        <p class="mt-2 text-sm text-gray-300">Your gateway to knowledge since 2023</p>
                    </div>
                    <div class="flex space-x-6">
                        <a href="#" class="hover:text-amber-300 transition">About</a>
                        <a href="#" class="hover:text-amber-300 transition">Contact</a>
                        <a href="#" class="hover:text-amber-300 transition">Privacy Policy</a>
                        <a href="#" class="hover:text-amber-300 transition">Terms</a>
                    </div>
                </div>
                <div class="mt-8 pt-6 border-t border-gray-700 text-center text-sm text-gray-400">
                    &copy; 2023 Academia Inspired Library Management System. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    reservations: {
        type: Array,
        default: () => []
    }
});

const activeStatus = ref(null);
const placeholderImage = '/api/placeholder/120/180';

const filteredReservations = computed(() => {
    if (!activeStatus.value) return props.reservations;

    return props.reservations.filter(reservation => {
        switch (activeStatus.value) {
            case 'active':
                return reservation.is_active && !reservation.is_expired && !reservation.is_canceled;
            case 'fulfilled':
                return reservation.is_fulfilled;
            case 'expired':
                return reservation.is_expired && !reservation.is_canceled;
            case 'canceled':
                return reservation.is_canceled;
            default:
                return true;
        }
    });
});

const getStatusText = (reservation) => {
    if (reservation.is_fulfilled) return 'Fulfilled';
    if (reservation.is_canceled) return 'Canceled';
    if (reservation.is_expired) return 'Expired';
    if (reservation.is_active) return 'Active';
    return 'Unknown';
};

const getBookCoverUrl = (book) => {
    return book.cover_image_url || placeholderImage;
};

const onImageError = (e) => {
    e.target.src = placeholderImage;
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};

const cancelReservation = async (reservationId) => {
    if (confirm('Are you sure you want to cancel this reservation?')) {
        try {
            await router.delete(`/reservations/${reservationId}`);
            router.reload({ only: ['reservations'] });
        } catch (error) {
            console.error('Failed to cancel reservation:', error);
        }
    }
};

const borrowBook = (bookId) => {
    router.post(`/books/${bookId}/borrow`, {}, {
        onSuccess: () => {
            router.reload({ only: ['reservations'] });
        }
    });
};
</script>

<style>
/* Consistent styling with books page */
button, a {
    transition: all 0.2s ease;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
