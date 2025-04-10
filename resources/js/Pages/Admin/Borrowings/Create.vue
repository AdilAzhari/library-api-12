<template>
    <AdminLayout>
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Manual Checkout</h1>
                <Link href="/admin/borrowings" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                    Back to Borrowings
                </Link>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <form @submit.prevent="submit">
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="book_id" class="block text-sm font-medium text-gray-700">Book *</label>
                                <select v-model="form.book_id" id="book_id"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                                    <option value="">Select Book</option>
                                    <option v-for="book in availableBooks" :key="book.id" :value="book.id">
                                        {{ book.title }} ({{ book.author }})
                                    </option>
                                </select>
                                <p v-if="form.errors.book_id" class="mt-2 text-sm text-red-600">{{
                                        form.errors.book_id
                                    }}</p>
                            </div>

                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700">User *</label>
                                <select v-model="form.user_id" id="user_id"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                                    <option value="">Select User</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }} ({{ user.email }})
                                    </option>
                                </select>
                                <p v-if="form.errors.user_id" class="mt-2 text-sm text-red-600">{{
                                        form.errors.user_id
                                    }}</p>
                            </div>

                            <div>
                                <label for="borrowed_at" class="block text-sm font-medium text-gray-700">Borrow
                                    Date</label>
                                <input v-model="form.borrowed_at" type="date" id="borrowed_at"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                                <p v-if="form.errors.borrowed_at" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.borrowed_at }}</p>
                            </div>

                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date *</label>
                                <input v-model="form.due_date" type="date" id="due_date"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                                <p v-if="form.errors.due_date" class="mt-2 text-sm text-red-600">{{
                                        form.errors.due_date
                                    }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-3 bg-gray-50 text-right border-t border-gray-200">
                        <button type="button" @click="reset"
                                class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                            Reset
                        </button>
                        <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#2c3e50] hover:bg-[#34495e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                            Check Out Book
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import {useForm} from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    availableBooks: Array,
    users: Array
});

// Set default dates
const today = new Date().toISOString().split('T')[0];
const twoWeeksLater = new Date();
twoWeeksLater.setDate(twoWeeksLater.getDate() + 14);
const dueDate = twoWeeksLater.toISOString().split('T')[0];

const form = useForm({
    book_id: '',
    user_id: '',
    borrowed_at: today,
    due_date: dueDate
});

const submit = () => {
    form.post('/admin/borrowings');
};

const reset = () => {
    form.reset();
    form.borrowed_at = today;
    form.due_date = dueDate;
};
</script>
