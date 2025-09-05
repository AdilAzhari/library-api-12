<template>
    <AuthenticatedLayout>
        <Head title="Pay Fine"/>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <Link href="/fines" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    ← Back to Fines
                </Link>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">Pay Fine</h1>
                <p class="mt-2 text-gray-600">Complete payment for your library fine</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Fine Details -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Fine Details</h3>
                    </div>
                    <div class="px-6 py-6">
                        <!-- Book Information -->
                        <div class="flex items-start space-x-4 mb-6">
                            <div class="flex-shrink-0 w-16 h-20 bg-gray-100 rounded flex items-center justify-center">
                                <img v-if="fine.borrow?.book?.cover_image_url" 
                                     :src="fine.borrow.book.cover_image_url" 
                                     :alt="fine.borrow.book.title"
                                     class="w-full h-full object-cover rounded">
                                <svg v-else class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-medium text-gray-900">
                                    {{ fine.borrow?.book?.title || 'Library Fine' }}
                                </h4>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ fine.borrow?.book?.author || '' }}
                                </p>
                                <div class="flex items-center mt-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                          :class="{
                                              'bg-red-100 text-red-800': fine.status === 'pending',
                                              'bg-yellow-100 text-yellow-800': fine.status === 'partial'
                                          }">
                                        {{ fine.status.replace('_', ' ').toUpperCase() }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Fine Information -->
                        <div class="space-y-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Fine Reason:</span>
                                <span class="font-medium text-gray-900">{{ fine.reason.replace('_', ' ') }}</span>
                            </div>
                            
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Created Date:</span>
                                <span class="font-medium text-gray-900">{{ formatDate(fine.created_at) }}</span>
                            </div>
                            
                            <div v-if="fine.due_date" class="flex justify-between text-sm">
                                <span class="text-gray-600">Due Date:</span>
                                <span class="font-medium text-gray-900">{{ formatDate(fine.due_date) }}</span>
                            </div>
                            
                            <hr>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Amount:</span>
                                <span class="text-xl font-bold text-gray-900">${{ fine.amount.toFixed(2) }}</span>
                            </div>
                            
                            <div v-if="fine.paid_amount > 0" class="flex justify-between text-sm">
                                <span class="text-gray-600">Already Paid:</span>
                                <span class="font-medium text-green-600">${{ fine.paid_amount.toFixed(2) }}</span>
                            </div>
                            
                            <div v-if="fine.paid_amount > 0" class="flex justify-between">
                                <span class="text-gray-600">Remaining Balance:</span>
                                <span class="text-xl font-bold text-red-600">${{ (fine.amount - fine.paid_amount).toFixed(2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Payment Information</h3>
                    </div>
                    <form @submit.prevent="submitPayment" class="px-6 py-6">
                        <!-- Payment Amount -->
                        <div class="mb-6">
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Amount
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input 
                                    id="amount"
                                    v-model="form.amount" 
                                    type="number" 
                                    step="0.01"
                                    :min="minimumPayment"
                                    :max="fine.amount - fine.paid_amount"
                                    class="block w-full pl-7 pr-12 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="0.00"
                                    required>
                                <div class="absolute inset-y-0 right-0 flex items-center">
                                    <select v-model="paymentType" 
                                            @change="updateAmount"
                                            class="h-full py-0 pl-2 pr-7 border-transparent bg-transparent text-gray-500 focus:ring-blue-500 focus:border-blue-500 rounded-md">
                                        <option value="partial">Partial</option>
                                        <option value="full">Full Payment</option>
                                    </select>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Minimum payment: ${{ minimumPayment.toFixed(2) }}
                            </p>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Method
                            </label>
                            <div class="grid grid-cols-1 gap-3">
                                <div v-for="method in paymentMethods" :key="method" 
                                     class="relative">
                                    <input 
                                        :id="`payment-${method}`"
                                        v-model="form.payment_method" 
                                        :value="method"
                                        type="radio" 
                                        class="sr-only">
                                    <label 
                                        :for="`payment-${method}`"
                                        class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50"
                                        :class="form.payment_method === method ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                        <div class="flex items-center space-x-3">
                                            <svg class="w-6 h-6" :class="form.payment_method === method ? 'text-blue-600' : 'text-gray-400'" 
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path v-if="method === 'card'" d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                                                <path v-if="method === 'card'" fillRule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clipRule="evenodd"/>
                                                
                                                <path v-if="method === 'bank_transfer'" fillRule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clipRule="evenodd"/>
                                                
                                                <path v-if="method === 'cash'" fillRule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clipRule="evenodd"/>
                                            </svg>
                                            <span class="text-sm font-medium" :class="form.payment_method === method ? 'text-blue-600' : 'text-gray-900'">
                                                {{ method.replace('_', ' ').toUpperCase() }}
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Reference -->
                        <div class="mb-6">
                            <label for="payment_reference" class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Reference (Optional)
                            </label>
                            <input 
                                id="payment_reference"
                                v-model="form.payment_reference" 
                                type="text" 
                                class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Transaction ID, check number, etc.">
                        </div>

                        <!-- Payment Summary -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Payment Summary</h4>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span>Payment Amount:</span>
                                    <span>${{ form.amount ? parseFloat(form.amount).toFixed(2) : '0.00' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Payment Method:</span>
                                    <span>{{ form.payment_method.replace('_', ' ').toUpperCase() }}</span>
                                </div>
                                <div class="flex justify-between font-medium pt-1 border-t">
                                    <span>Remaining After Payment:</span>
                                    <span>${{ (fine.amount - fine.paid_amount - (parseFloat(form.amount) || 0)).toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            :disabled="!form.amount || form.processing"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Processing Payment...' : 'Process Payment' }}
                        </button>

                        <p class="text-xs text-gray-500 text-center mt-4">
                            By proceeding, you agree to process this payment and acknowledge that it may take 1-2 business days to reflect in your account.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    fine: Object,
    paymentMethods: Array,
    minimumPayment: Number,
})

const paymentType = ref('full')

const form = useForm({
    amount: props.fine.amount - props.fine.paid_amount,
    payment_method: props.paymentMethods[0] || 'card',
    payment_reference: '',
})

const updateAmount = () => {
    if (paymentType.value === 'full') {
        form.amount = props.fine.amount - props.fine.paid_amount
    } else {
        form.amount = props.minimumPayment
    }
}

const submitPayment = () => {
    form.post(`/fines/${props.fine.id}/pay`, {
        onSuccess: () => {
            // Payment successful - user will be redirected
        },
        onError: (errors) => {
            // Handle validation errors
            console.error('Payment failed:', errors)
        }
    })
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', { 
        year: 'numeric',
        month: 'long', 
        day: 'numeric' 
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