<template>
    <AdminLayout>
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ form.id ? 'Edit Book' : 'Add New Book' }}
                </h1>
                <Link href="/admin/books" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                    Back to Books
                </Link>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <form @submit.prevent="submit">
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                                <input v-model="form.title" type="text" id="title" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                                <p v-if="form.errors.title" class="mt-2 text-sm text-red-600">{{ form.errors.title }}</p>
                            </div>

                            <div>
                                <label for="author" class="block text-sm font-medium text-gray-700">Author *</label>
                                <input v-model="form.author" type="text" id="author" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                                <p v-if="form.errors.author" class="mt-2 text-sm text-red-600">{{ form.errors.author }}</p>
                            </div>

                            <div>
                                <label for="ISBN" class="block text-sm font-medium text-gray-700">ISBN *</label>
                                <input v-model="form.ISBN" type="text" id="ISBN" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                                <p v-if="form.errors.ISBN" class="mt-2 text-sm text-red-600">{{ form.errors.ISBN }}</p>
                            </div>

                            <div>
                                <label for="publication_year" class="block text-sm font-medium text-gray-700">Publication Year</label>
                                <input v-model="form.publication_year" type="number" id="publication_year" min="1900" :max="new Date().getFullYear()" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                                <p v-if="form.errors.publication_year" class="mt-2 text-sm text-red-600">{{ form.errors.publication_year }}</p>
                            </div>

                            <div>
                                <label for="genre_id" class="block text-sm font-medium text-gray-700">Genre</label>
                                <select v-model="form.genre_id" id="genre_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                                    <option value="">Select Genre</option>
                                    <option v-for="genre in genres" :key="genre.id" :value="genre.id">{{ genre.name }}</option>
                                </select>
                                <p v-if="form.errors.genre_id" class="mt-2 text-sm text-red-600">{{ form.errors.genre_id }}</p>
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                                <select v-model="form.status" id="status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                                    <option value="available">Available</option>
                                    <option value="reserved">Reserved</option>
                                    <option value="borrowed">Borrowed</option>
                                </select>
                                <p v-if="form.errors.status" class="mt-2 text-sm text-red-600">{{ form.errors.status }}</p>
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea v-model="form.description" id="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm"></textarea>
                            <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cover Image</label>
                            <div class="mt-1 flex items-center">
                                <img v-if="form.cover_image_url" :src="form.cover_image_url" class="h-16 w-16 object-cover rounded-md">
                                <span v-else class="flex items-center justify-center h-16 w-16 rounded-md bg-gray-200">
                  <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </span>
                                <button type="button" class="ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                                    Change
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-3 bg-gray-50 text-right border-t border-gray-200">
                        <button type="button" @click="reset" class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                            Reset
                        </button>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#2c3e50] hover:bg-[#34495e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                            {{ form.id ? 'Update' : 'Save' }} Book
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    book: Object,
    genres: Array
});

const form = useForm({
    id: props.book?.id || null,
    title: props.book?.title || '',
    author: props.book?.author || '',
    ISBN: props.book?.ISBN || '',
    description: props.book?.description || '',
    publication_year: props.book?.publication_year || '',
    genre_id: props.book?.genre_id || '',
    status: props.book?.status || 'available',
    cover_image: null,
    cover_image_url: props.book?.cover_image_url || null
});

const submit = () => {
    if (form.id) {
        form.put(`/admin/books/${form.id}`);
    } else {
        form.post('/admin/books');
    }
};

const reset = () => {
    if (form.id) {
        form.title = props.book.title;
        form.author = props.book.author;
        form.ISBN = props.book.ISBN;
        form.description = props.book.description;
        form.publication_year = props.book.publication_year;
        form.genre_id = props.book.genre_id;
        form.status = props.book.status;
    } else {
        form.reset();
    }
};
</script>
