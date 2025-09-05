<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <Header/>
        
        <main class="container mx-auto px-4 py-8 max-w-4xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Submit a Review</h1>
        <form @submit.prevent="submitForm" class="bg-white rounded-lg shadow-md p-6">
            <div class="space-y-4">
                <div>
                    <label for="book_id" class="block text-sm font-medium text-gray-700">Book ID</label>
                    <input
                        v-model="form.book_id"
                        type="number"
                        id="book_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700">User ID</label>
                    <input
                        v-model="form.user_id"
                        type="number"
                        id="user_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>
                <div>
                    <label for="rating" class="block text-sm font-medium text-gray-700">Rating</label>
                    <input
                        v-model="form.rating"
                        type="number"
                        id="rating"
                        min="1"
                        max="5"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>
                <div>
                    <label for="comment" class="block text-sm font-medium text-gray-700">Comment</label>
                    <textarea
                        v-model="form.comment"
                        id="comment"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                </div>
                <div>
                    <button
                        type="submit"
                        class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors duration-300"
                    >
                        Submit Review
                    </button>
                </div>
            </div>
        </form>
        </main>
        
        <!-- Footer -->
        <Footer :year="new Date().getFullYear()" />
    </div>
</template>

<script setup>
import {ref} from 'vue';
import {router} from '@inertiajs/vue3';
import Header from '@/Components/AppHeader.vue';
import Footer from '@/Components/AppFooter.vue';

const form = ref({
    book_id: '',
    user_id: '',
    rating: '',
    comment: '',
});

const submitForm = () => {
    router.post('/review', form.value);
};
</script>
