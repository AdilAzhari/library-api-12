<template>
    <AuthenticatedLayout>
        <Head title="Return Book"/>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <Link href="/borrows" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    ← Back to My Borrows
                </Link>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">Return Book</h1>
                <p class="mt-2 text-gray-600">Complete the return process for your borrowed book</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Book Details -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Book Information</h3>
                    </div>
                    <div class="px-6 py-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-20 h-28 bg-gray-100 rounded flex items-center justify-center">
                                <img v-if="borrow.book.cover_image_url" 
                                     :src="borrow.book.cover_image_url" 
                                     :alt="borrow.book.title"
                                     class="w-full h-full object-cover rounded">
                                <svg v-else class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-medium text-gray-900">{{ borrow.book.title }}</h4>
                                <p class="text-sm text-gray-600 mt-1">by {{ borrow.book.author }}</p>
                                <p class="text-xs text-gray-500 mt-2">ISBN: {{ borrow.book.isbn || 'N/A' }}</p>
                                <p class="text-xs text-gray-500">Genre: {{ borrow.book.genre?.name || 'N/A' }}</p>
                            </div>
                        </div>
                        
                        <div class="mt-6 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Borrowed Date:</span>
                                <span class="font-medium text-gray-900">{{ formatDate(borrow.borrowed_at) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Due Date:</span>
                                <span class="font-medium" :class="isOverdue ? 'text-red-600' : 'text-gray-900'">
                                    {{ formatDate(borrow.due_date) }}
                                    <span v-if="isOverdue" class="text-red-600 font-bold ml-1">
                                        ({{ daysOverdue }} days overdue)
                                    </span>
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Days Borrowed:</span>
                                <span class="font-medium text-gray-900">{{ daysBorrowed }} days</span>
                            </div>
                            <div v-if="borrow.renewals_count > 0" class="flex justify-between text-sm">
                                <span class="text-gray-600">Renewals:</span>
                                <span class="font-medium text-blue-600">{{ borrow.renewals_count }} time(s)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Return Form -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Return Details</h3>
                    </div>
                    <form @submit.prevent="submitReturn" class="px-6 py-6">
                        <!-- Book Condition -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Book Condition *
                            </label>
                            <div class="space-y-2">
                                <div v-for="condition in bookConditions" :key="condition.value" class="flex items-center">
                                    <input :id="`condition-${condition.value}`"
                                           v-model="form.condition" 
                                           :value="condition.value"
                                           type="radio" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                           required>
                                    <label :for="`condition-${condition.value}`" class="ml-3 block text-sm font-medium text-gray-700">
                                        {{ condition.label }}
                                        <span class="text-gray-500 block text-xs">{{ condition.description }}</span>
                                    </label>
                                </div>
                            </div>
                            <p v-if="form.errors.condition" class="text-red-600 text-sm mt-1">{{ form.errors.condition }}</p>
                        </div>

                        <!-- Return Location -->
                        <div class="mb-6">
                            <label for="return_location" class="block text-sm font-medium text-gray-700 mb-2">
                                Return Location
                            </label>
                            <select id="return_location"
                                    v-model="form.return_location" 
                                    class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-300': form.errors.return_location }">
                                <option value="">Select return location</option>
                                <option v-for="location in returnLocations" :key="location" :value="location">
                                    {{ location }}
                                </option>
                            </select>
                            <p v-if="form.errors.return_location" class="text-red-600 text-sm mt-1">{{ form.errors.return_location }}</p>
                        </div>

                        <!-- Return Notes -->
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Additional Notes (Optional)
                            </label>
                            <textarea id="notes"
                                      v-model="form.notes" 
                                      rows="3"
                                      class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                      :class="{ 'border-red-300': form.errors.notes }"
                                      placeholder="Any additional comments about the book condition or return..."></textarea>
                            <p v-if="form.errors.notes" class="text-red-600 text-sm mt-1">{{ form.errors.notes }}</p>
                        </div>

                        <!-- Late Fee Warning -->
                        <div v-if="isOverdue" class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Late Return Fee</h3>
                                    <p class="text-sm text-red-700 mt-1">
                                        This book is {{ daysOverdue }} days overdue. A late fee of ${{ calculatedFine.toFixed(2) }} will be applied to your account.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Damage Fee Warning -->
                        <div v-if="form.condition === 'damaged' || form.condition === 'lost'" class="mb-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">{{ form.condition === 'lost' ? 'Book Replacement Fee' : 'Damage Assessment Required' }}</h3>
                                    <p class="text-sm text-yellow-700 mt-1">
                                        {{ form.condition === 'lost' 
                                            ? `You will be charged the full replacement cost of this book ($${borrow.book.replacement_cost || '25.00'}).`
                                            : 'A library staff member will assess the damage and determine if any fees apply.' 
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Return Summary -->
                        <div class="mb-6 bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Return Summary</h4>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span>Return Date:</span>
                                    <span>{{ formatDate(new Date()) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Days Borrowed:</span>
                                    <span>{{ daysBorrowed }} days</span>
                                </div>
                                <div v-if="isOverdue" class="flex justify-between text-red-600">
                                    <span>Late Fee:</span>
                                    <span>${{ calculatedFine.toFixed(2) }}</span>
                                </div>
                                <div v-if="form.condition === 'lost'" class="flex justify-between text-red-600">
                                    <span>Replacement Cost:</span>
                                    <span>${{ borrow.book.replacement_cost || '25.00' }}</span>
                                </div>
                                <div v-if="form.condition === 'damaged'" class="flex justify-between text-yellow-600">
                                    <span>Damage Assessment:</span>
                                    <span>Pending</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Processing Return...' : 'Complete Return' }}
                        </button>

                        <p class="text-xs text-gray-500 text-center mt-4">
                            By completing this return, you acknowledge that you have returned the book in the condition specified above.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    borrow: Object,
    returnLocations: Array,
})

const bookConditions = [
    {
        value: 'excellent',
        label: 'Excellent',
        description: 'No visible wear, like new'
    },
    {
        value: 'good',
        label: 'Good',
        description: 'Minor wear, all pages intact'
    },
    {
        value: 'fair',
        label: 'Fair',
        description: 'Noticeable wear but fully readable'
    },
    {
        value: 'damaged',
        label: 'Damaged',
        description: 'Significant damage, missing pages, or water damage'
    },
    {
        value: 'lost',
        label: 'Lost',
        description: 'Book is lost or destroyed'
    }
]

const form = useForm({
    condition: 'good',
    return_location: '',
    notes: '',
})

const isOverdue = computed(() => {
    return new Date(props.borrow.due_date) < new Date()
})

const daysOverdue = computed(() => {
    if (!isOverdue.value) return 0
    const dueDate = new Date(props.borrow.due_date)
    const today = new Date()
    const timeDiff = today.getTime() - dueDate.getTime()
    return Math.ceil(timeDiff / (1000 * 3600 * 24))
})

const daysBorrowed = computed(() => {
    const borrowedDate = new Date(props.borrow.borrowed_at)
    const today = new Date()
    const timeDiff = today.getTime() - borrowedDate.getTime()
    return Math.ceil(timeDiff / (1000 * 3600 * 24))
})

const calculatedFine = computed(() => {
    if (!isOverdue.value) return 0
    // $0.50 per day late fee
    return daysOverdue.value * 0.50
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', { 
        year: 'numeric',
        month: 'long', 
        day: 'numeric' 
    })
}

const submitReturn = () => {
    form.post(`/borrows/${props.borrow.id}/return`, {
        onSuccess: () => {
            // Return successful - user will be redirected
        },
        onError: (errors) => {
            // Handle validation errors
            console.error('Return failed:', errors)
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
</style>"