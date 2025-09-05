<template>
    <AuthenticatedLayout>
        <Head title="Borrow Book"/>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <Link :href="`/books/${book.id}`" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    ← Back to {{ book.title }}
                </Link>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">Borrow Book</h1>
                <p class="mt-2 text-gray-600">Complete your borrowing request and we'll prepare the book for pickup</p>
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
                                
                                <!-- Rating -->
                                <div class="flex items-center mb-4" v-if="book.average_rating">
                                    <div class="flex">
                                        <svg v-for="star in 5" :key="star" 
                                             :class="star <= Math.floor(book.average_rating) ? 'text-yellow-400' : 'text-gray-300'" 
                                             class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">{{ book.average_rating.toFixed(1) }} ({{ book.reviews_count || 0 }} reviews)</span>
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
                                    <div v-if="book.publisher">
                                        <span class="text-gray-600">Publisher:</span>
                                        <span class="ml-2 text-gray-900">{{ book.publisher }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Book Description -->
                        <div v-if="book.description" class="mt-6">
                            <h5 class="text-sm font-medium text-gray-900 mb-2">Description</h5>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ book.description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Borrow Form -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Borrowing Details</h3>
                    </div>
                    <form @submit.prevent="submitBorrow" class="px-6 py-6">
                        <!-- Loan Period -->
                        <div class="mb-6">
                            <label for="loan_period" class="block text-sm font-medium text-gray-700 mb-2">
                                Loan Period
                            </label>
                            <select id="loan_period"
                                    v-model="form.loan_period" 
                                    class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-300': form.errors.loan_period }">
                                <option v-for="period in loanPeriods" :key="period.value" :value="period.value">
                                    {{ period.label }} - Due {{ formatDueDate(period.value) }}
                                </option>
                            </select>
                            <p v-if="form.errors.loan_period" class="text-red-600 text-sm mt-1">{{ form.errors.loan_period }}</p>
                        </div>

                        <!-- Pickup Location -->
                        <div class="mb-6">
                            <label for="pickup_location" class="block text-sm font-medium text-gray-700 mb-2">
                                Pickup Location
                            </label>
                            <select id="pickup_location"
                                    v-model="form.pickup_location" 
                                    class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-300': form.errors.pickup_location }">
                                <option value="">Select pickup location</option>
                                <option v-for="location in pickupLocations" :key="location" :value="location">
                                    {{ location }}
                                </option>
                            </select>
                            <p v-if="form.errors.pickup_location" class="text-red-600 text-sm mt-1">{{ form.errors.pickup_location }}</p>
                        </div>

                        <!-- Delivery Method -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Delivery Method
                            </label>
                            <div class="space-y-2">
                                <div v-for="method in deliveryMethods" :key="method.value" class="flex items-center">
                                    <input :id="`delivery-${method.value}`"
                                           v-model="form.delivery_method" 
                                           :value="method.value"
                                           type="radio" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <label :for="`delivery-${method.value}`" class="ml-3 block text-sm text-gray-700">
                                        <span class="font-medium">{{ method.label }}</span>
                                        <span v-if="method.description" class="text-gray-500 block text-xs">{{ method.description }}</span>
                                        <span v-if="method.fee" class="text-green-600 block text-xs">Fee: ${{ method.fee.toFixed(2) }}</span>
                                    </label>
                                </div>
                            </div>
                            <p v-if="form.errors.delivery_method" class="text-red-600 text-sm mt-1">{{ form.errors.delivery_method }}</p>
                        </div>

                        <!-- Reading List -->
                        <div class="mb-6">
                            <label for="reading_list" class="block text-sm font-medium text-gray-700 mb-2">
                                Add to Reading List (Optional)
                            </label>
                            <select id="reading_list"
                                    v-model="form.reading_list_id" 
                                    class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Don't add to any list</option>
                                <option v-for="list in readingLists" :key="list.id" :value="list.id">
                                    {{ list.name }} ({{ list.books_count || 0 }} books)
                                </option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                You can organize this book in one of your reading lists
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
                                      :class="{ 'border-red-300': form.errors.notes }"
                                      placeholder="Any special handling requirements or pickup instructions..."></textarea>
                            <p v-if="form.errors.notes" class="text-red-600 text-sm mt-1">{{ form.errors.notes }}</p>
                        </div>

                        <!-- Borrowing Summary -->
                        <div class="mb-6 bg-blue-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-900 mb-3">Borrowing Summary</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-blue-700">Book:</span>
                                    <span class="font-medium text-blue-900">{{ book.title }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-blue-700">Borrow Date:</span>
                                    <span class="font-medium text-blue-900">{{ formatDate(new Date()) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-blue-700">Due Date:</span>
                                    <span class="font-medium text-blue-900">{{ formatDueDate(form.loan_period) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-blue-700">Pickup Method:</span>
                                    <span class="font-medium text-blue-900">{{ getDeliveryMethodLabel(form.delivery_method) }}</span>
                                </div>
                                <div v-if="form.pickup_location" class="flex justify-between">
                                    <span class="text-blue-700">Location:</span>
                                    <span class="font-medium text-blue-900">{{ form.pickup_location }}</span>
                                </div>
                                <div v-if="getDeliveryFee(form.delivery_method) > 0" class="flex justify-between border-t border-blue-200 pt-2">
                                    <span class="text-blue-700">Delivery Fee:</span>
                                    <span class="font-bold text-blue-900">${{ getDeliveryFee(form.delivery_method).toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input id="terms"
                                       v-model="form.accept_terms" 
                                       type="checkbox" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                       required>
                                <label for="terms" class="ml-3 block text-sm text-gray-700">
                                    I agree to the <a href="/terms" target="_blank" class="text-blue-600 hover:text-blue-800">borrowing terms and conditions</a>
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
                                <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Processing Borrow...' : 'Confirm Borrowing' }}
                        </button>

                        <p class="text-xs text-gray-500 text-center mt-4">
                            You will receive a confirmation email with pickup instructions once your borrowing request is approved.
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
    readingLists: Array,
})

const loanPeriods = [
    { value: 14, label: '2 weeks (Standard)' },
    { value: 21, label: '3 weeks (Extended)' },
    { value: 7, label: '1 week (Quick read)' }
]

const deliveryMethods = [
    {
        value: 'pickup',
        label: 'Library Pickup',
        description: 'Pick up from selected library location',
        fee: 0
    },
    {
        value: 'campus_delivery',
        label: 'Campus Delivery',
        description: 'Delivery to campus location (1-2 business days)',
        fee: 2.50
    },
    {
        value: 'home_delivery',
        label: 'Home Delivery',
        description: 'Delivery to your home address (3-5 business days)',
        fee: 5.00
    }
]

const form = useForm({
    loan_period: 14,
    pickup_location: '',
    delivery_method: 'pickup',
    reading_list_id: '',
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

const formatDueDate = (days) => {
    if (!days) return ''
    const dueDate = new Date()
    dueDate.setDate(dueDate.getDate() + days)
    return formatDate(dueDate)
}

const getDeliveryMethodLabel = (value) => {
    const method = deliveryMethods.find(m => m.value === value)
    return method ? method.label : ''
}

const getDeliveryFee = (value) => {
    const method = deliveryMethods.find(m => m.value === value)
    return method ? method.fee : 0
}

const submitBorrow = () => {
    form.post(`/books/${props.book.id}/borrow`, {
        onSuccess: () => {
            // Borrow successful - user will be redirected
        },
        onError: (errors) => {
            // Handle validation errors
            console.error('Borrow failed:', errors)
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