<template>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Borrow a Book</h1>
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
                    <button
                        type="submit"
                        class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors duration-300"
                    >
                        Borrow Book
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const form = ref({
    book_id: '',
    user_id: '',
});

const submitForm = () => {
    router.post('/borrow', form.value);
};
</script>
