<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <AppHeader/>

        <main class="container mx-auto px-4 py-8 max-w-4xl">
            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-3xl font-serif font-bold text-[#2c3e50] flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-500" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Edit Book</span>
                </h1>
                <Link
                    href="/books"
                    class="border border-[#2c3e50] text-[#2c3e50] px-5 py-2 rounded-lg font-medium hover:bg-[#e8e3d5] transition-all duration-300"
                >
                    Back to Books
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-[#e8e3d5]">
                <div class="h-2 bg-gradient-to-r from-[#8b5a2b] to-[#d4a76a]"></div>

                <form @submit.prevent="submitForm" class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Left column -->
                        <div class="space-y-6">
                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-[#2c3e50] mb-2">
                                    Book Title <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.title"
                                    type="text"
                                    id="title"
                                    class="w-full rounded-lg border-[#e8e3d5] shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 transition duration-300"
                                    required
                                    maxlength="255"
                                />
                                <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
                            </div>

                            <!-- Author -->
                            <div>
                                <label for="author" class="block text-sm font-medium text-[#2c3e50] mb-2">
                                    Author <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.author"
                                    type="text"
                                    id="author"
                                    class="w-full rounded-lg border-[#e8e3d5] shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 transition duration-300"
                                    required
                                    maxlength="255"
                                />
                                <p v-if="errors.author" class="mt-1 text-sm text-red-600">{{ errors.author }}</p>
                            </div>

                            <!-- Publication Year -->
                            <div>
                                <label for="publication_year" class="block text-sm font-medium text-[#2c3e50] mb-2">
                                    Publication Year <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model.number="form.publication_year"
                                    type="number"
                                    id="publication_year"
                                    class="w-full rounded-lg border-[#e8e3d5] shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 transition duration-300"
                                    min="1900"
                                    :max="new Date().getFullYear()"
                                    required
                                />
                                <p v-if="errors.publication_year" class="mt-1 text-sm text-red-600">
                                    {{ errors.publication_year }}
                                </p>
                            </div>

                            <!-- Genre Selection -->
                            <div>
                                <label for="genre_id" class="block text-sm font-medium text-[#2c3e50] mb-2">
                                    Genre <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.genre_id"
                                    id="genre_id"
                                    class="w-full rounded-lg border-[#e8e3d5] shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 transition duration-300"
                                    required
                                >
                                    <option v-for="genre in genres" :key="genre.id" :value="genre.id">
                                        {{ genre.name }}
                                    </option>
                                </select>
                                <p v-if="errors.genre_id" class="mt-1 text-sm text-red-600">{{ errors.genre_id }}</p>
                            </div>
                        </div>

                        <!-- Right column -->
                        <div class="space-y-6">
                            <!-- Cover Image -->
                            <div>
                                <label class="block text-sm font-medium text-[#2c3e50] mb-2">Cover Image</label>
                                <input
                                    type="file"
                                    id="cover_image"
                                    @change="handleFileUpload"
                                    accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                    class="w-full rounded-lg border-[#e8e3d5] shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 transition duration-300"
                                />
                                <div v-if="currentCover" class="mt-3">
                                    <p class="text-sm text-gray-500 mb-1">Current Cover:</p>
                                    <img :src="currentCover"
                                         class="h-40 w-32 object-cover rounded-md border border-[#e8e3d5]"/>
                                </div>
                                <p v-if="errors.cover_image" class="mt-1 text-sm text-red-600">
                                    {{ errors.cover_image }}
                                </p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-[#2c3e50] mb-2">
                                    Description
                                </label>
                                <textarea
                                    v-model="form.description"
                                    id="description"
                                    rows="7"
                                    class="w-full rounded-lg border-[#e8e3d5] shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 transition duration-300"
                                ></textarea>
                                <p v-if="errors.description" class="mt-1 text-sm text-red-600">
                                    {{ errors.description }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional fields -->
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- ISBN -->
                        <div>
                            <label for="isbn" class="block text-sm font-medium text-[#2c3e50] mb-2">ISBN</label>
                            <input
                                v-model="form.ISBN"
                                type="text"
                                id="isbn"
                                class="w-full rounded-lg border-[#e8e3d5] shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 transition duration-300"
                                maxlength="17"
                            />
                            <p v-if="errors.ISBN" class="mt-1 text-sm text-red-600">{{ errors.ISBN }}</p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-[#2c3e50] mb-2">Status</label>
                            <select
                                v-model="form.status"
                                id="status"
                                class="w-full rounded-lg border-[#e8e3d5] shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 transition duration-300"
                            >
                                <option value="available">Available</option>
                                <option value="borrowed">Borrowed</option>
                                <option value="reserved">Reserved</option>
                            </select>
                            <p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status }}</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-10 flex justify-end border-t border-[#e8e3d5] pt-6">
                        <button
                            type="submit"
                            :disabled="processing"
                            class="px-8 py-3 bg-gradient-to-r from-[#2c3e50] to-[#8b5a2b] rounded-lg text-sm font-medium text-white hover:from-[#34495e] hover:to-[#9c6b3a] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 shadow-md transition-all duration-300 disabled:opacity-70"
                        >
                            <span v-if="processing">Updating...</span>
                            <span v-else>Update Book</span>
                        </button>
                    </div>
                </form>
            </div>
        </main>

        <AppFooter/>
    </div>
</template>

<script setup>
import {ref, computed} from 'vue';
import {router} from '@inertiajs/vue3';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';

const props = defineProps({
    book: Object,
    genres: Array,
    errors: Object,
    currentCover: String,
});

const processing = ref(false);

const form = ref({
    title: props.book.title,
    author: props.book.author,
    description: props.book.description,
    publication_year: props.book.publication_year,
    genre_id: props.book.genre_id,
    ISBN: props.book.ISBN,
    status: props.book.status,
    cover_image: null,
});

const handleFileUpload = (event) => {
    form.value.cover_image = event.target.files[0];
};

const submitForm = () => {
    processing.value = true;

    const formData = new FormData();
    for (const key in form.value) {
        if (form.value[key] !== null) {
            formData.append(key, form.value[key]);
        }
    }
    formData.append('_method', 'PUT');

    router.post(`/books/${props.book.id}`, formData, {
        preserveScroll: true,
        onSuccess: () => {
            processing.value = false;
        },
        onError: () => {
            processing.value = false;
        }
    });
};
</script>
