<template>
    <AuthenticatedLayout>
        <Head title="Reserve Book"/>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <Link :href="`/books/${book.id}`" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    ← Back to {{ book.title }}
                </Link>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">Reserve Book</h1>
                <p class="mt-2 text-gray-600">Join the queue for this book and we'll notify you when it's available</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Book Details -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Book Information</h3>
                    </div>
                    <div class="px-6 py-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-24 h-32 bg-gray-100 rounded flex items-center justify-center">
                                <img v-if="book.cover_image_url" 
                                     :src="book.cover_image_url" 
                                     :alt="book.title"
                                     class="w-full h-full object-cover rounded">
                                <svg v-else class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xl font-medium text-gray-900 mb-2">{{ book.title }}</h4>
                                <p class="text-gray-600 mb-1">by {{ book.author }}</p>
                                <p class="text-sm text-gray-500 mb-3">{{ book.genre?.name || 'No genre specified' }}</p>
                                
                                <!-- Current Status -->
                                <div class="mb-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                          :class="{
                                              'bg-red-100 text-red-800': book.status === 'borrowed',
                                              'bg-yellow-100 text-yellow-800': book.status === 'reserved'
                                          }">
                                        {{ book.status === 'borrowed' ? 'Currently Borrowed' : 'Currently Reserved' }}
                                    </span>
                                </div>
                                
                                <!-- Book Details -->
                                <div class="space-y-2 text-sm">
                                    <div v-if="book.isbn">
                                        <span class="text-gray-600">ISBN:</span>
                                        <span class="ml-2 text-gray-900">{{ book.isbn }}</span>
                                    </div>
                                    <div v-if="book.publication_year">
                                        <span class="text-gray-600">Published:</span>
                                        <span class="ml-2 text-gray-900">{{ book.publication_year }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reservation Form -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Reservation Details</h3>
                    </div>
                    <form @submit.prevent="submitReservation" class="px-6 py-6">
                        <!-- Queue Information -->
                        <div v-if="queueInfo" class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd"/>
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800">Queue Information</h4>
                                    <p class="text-sm text-blue-700 mt-1">
                                        You will be {{ queueInfo.position }}{{ getOrdinalSuffix(queueInfo.position) }} in line. 
                                        Estimated wait time: {{ queueInfo.estimated_wait_days }} days.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Pickup Location -->
                        <div class="mb-6">
                            <label for="pickup_location" class="block text-sm font-medium text-gray-700 mb-2">
                                Preferred Pickup Location *
                            </label>
                            <select id="pickup_location"
                                    v-model="form.pickup_location" 
                                    class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-300': form.errors.pickup_location }"
                                    required>
                                <option value="">Select pickup location</option>
                                <option v-for="location in pickupLocations" :key="location" :value="location">
                                    {{ location }}
                                </option>
                            </select>
                            <p v-if="form.errors.pickup_location" class="text-red-600 text-sm mt-1">{{ form.errors.pickup_location }}</p>
                        </div>

                        <!-- Notification Preferences -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Notification Preferences
                            </label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input id="email_notification"
                                           v-model="form.email_notification" 
                                           type="checkbox" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="email_notification" class="ml-3 block text-sm text-gray-700">
                                        Email me when the book is ready for pickup
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="sms_notification"
                                           v-model="form.sms_notification" 
                                           type="checkbox" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="sms_notification" class="ml-3 block text-sm text-gray-700">
                                        Send SMS notification (if phone number provided)
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="reminder_notification"
                                           v-model="form.reminder_notification" 
                                           type="checkbox" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="reminder_notification" class="ml-3 block text-sm text-gray-700">
                                        Send reminder before reservation expires
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Priority Level -->
                        <div class="mb-6">
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                                Priority Level
                            </label>
                            <select id="priority"
                                    v-model="form.priority" 
                                    class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="normal">Normal - Free</option>
                                <option value="high">High Priority - $2.00 (Move up in queue)</option>
                                <option value="urgent">Urgent - $5.00 (Next available)</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                Higher priority levels may reduce your wait time but incur additional fees.
                            </p>
                        </div>

                        <!-- Special Instructions -->
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Special Instructions (Optional)
                            </label>
                            <textarea id="notes"
                                      v-model="form.notes" 
                                      rows="3"
                                      class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Any special requests or delivery instructions..."></textarea>
                        </div>

                        <!-- Reservation Summary -->
                        <div class="mb-6 bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Reservation Summary</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Book:</span>
                                    <span class="font-medium text-gray-900">{{ book.title }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Reservation Date:</span>
                                    <span class="font-medium text-gray-900">{{ formatDate(new Date()) }}</span>
                                </div>
                                <div v-if="queueInfo" class="flex justify-between">
                                    <span class="text-gray-600">Queue Position:</span>
                                    <span class="font-medium text-gray-900">#{{ queueInfo.position }}</span>
                                </div>
                                <div v-if="queueInfo" class="flex justify-between">
                                    <span class="text-gray-600">Estimated Wait:</span>
                                    <span class="font-medium text-gray-900">{{ queueInfo.estimated_wait_days }} days</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Pickup Location:</span>
                                    <span class="font-medium text-gray-900">{{ form.pickup_location || 'Not selected' }}</span>
                                </div>
                                <div v-if="getPriorityFee(form.priority) > 0" class="flex justify-between border-t border-gray-200 pt-2">
                                    <span class="text-gray-600">Priority Fee:</span>
                                    <span class="font-bold text-gray-900">${{ getPriorityFee(form.priority).toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-6">
                            <div class="flex items-start">
                                <input id="terms"
                                       v-model="form.accept_terms" 
                                       type="checkbox" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-0.5"
                                       required>
                                <label for="terms" class="ml-3 block text-sm text-gray-700">
                                    I agree to the <a href="/terms" target="_blank" class="text-blue-600 hover:text-blue-800">reservation terms and conditions</a>. 
                                    I understand that I have 7 days to pick up the book once notified, or my reservation will expire.
                                </label>
                            </div>
                            <p v-if="form.errors.accept_terms" class="text-red-600 text-sm mt-1">{{ form.errors.accept_terms }}</p>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            :disabled="form.processing || !form.accept_terms"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Creating Reservation...' : 'Reserve This Book' }}
                        </button>

                        <p class="text-xs text-gray-500 text-center mt-4">
                            You will receive a confirmation email and be notified when the book becomes available for pickup.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    book: Object,
    pickupLocations: Array,
    queueInfo: Object,
})

const form = useForm({
    pickup_location: '',
    email_notification: true,
    sms_notification: false,
    reminder_notification: true,
    priority: 'normal',
    notes: '',
    accept_terms: false,
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { 
        year: 'numeric',
        month: 'long', 
        day: 'numeric' 
    })
}

const getOrdinalSuffix = (num) => {
    const j = num % 10
    const k = num % 100
    if (j === 1 && k !== 11) {
        return 'st'
    }
    if (j === 2 && k !== 12) {
        return 'nd'
    }
    if (j === 3 && k !== 13) {
        return 'rd'
    }
    return 'th'
}

const getPriorityFee = (priority) => {
    switch (priority) {
        case 'high':
            return 2.00
        case 'urgent':
            return 5.00
        default:
            return 0.00
    }
}

const submitReservation = () => {
    form.post(`/reservations/books/${props.book.id}/reserve`, {
        onSuccess: () => {
            // Reservation successful - user will be redirected
        },
        onError: (errors) => {
            // Handle validation errors
            console.error('Reservation failed:', errors)
        }
    })
}
</script>

<style scoped>
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>