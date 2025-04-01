<template>
    <div class="min-h-screen bg-[#f9f7f2]">
        <AppHeader/>

        <main class="container mx-auto px-4 py-8 max-w-4xl">
            <!-- Flash Messages -->
            <div class="mb-6 space-y-3">
                <FlashMessage
                    v-if="flash.success"
                    type="success"
                    :message="flash.success"
                    @close="flash.success = null"
                />
                <FlashMessage
                    v-if="flash.error"
                    type="error"
                    :message="flash.error"
                    @close="flash.error = null"
                />
            </div>
            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-3xl font-serif font-bold text-[#2c3e50] flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-500" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Add New Book</span>
                </h1>
                <Link
                    href="/books"
                    class="border border-[#2c3e50] text-[#2c3e50] px-5 py-2 rounded-lg font-medium hover:bg-[#e8e3d5] transition-all duration-300"
                >
                    Back to Books
                </Link>
            </div>

            <!-- Form with book spine effect -->
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
                                    placeholder="Enter the book title"
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
                                    placeholder="Enter the author's name"
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
                                    v-model="form.publication_year"
                                    type="number"
                                    id="publication_year"
                                    placeholder="e.g. 2023"
                                    class="w-full rounded-lg border-[#e8e3d5] shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 transition duration-300"
                                    min="1900"
                                    :max="new Date().getFullYear()"
                                    required
                                />
                                <p v-if="errors.publication_year" class="mt-1 text-sm text-red-600">
                                    {{ errors.publication_year }}</p>
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
                                    <option value="" disabled>Select a genre</option>
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
                                <div
                                    class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-[#e8e3d5] border-dashed rounded-lg">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                             viewBox="0 0 48 48">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="cover_image"
                                                   class="relative cursor-pointer bg-white rounded-md font-medium text-amber-600 hover:text-amber-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-amber-500">
                                                <span>Upload a file</span>
                                                <input
                                                    id="cover_image"
                                                    type="file"
                                                    class="sr-only"
                                                    accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                                    @change="handleFileUpload"
                                                >
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF, WEBP up to 2MB</p>
                                    </div>
                                </div>
                                <div v-if="imagePreview" class="mt-3">
                                    <img :src="imagePreview" alt="Cover preview"
                                         class="h-40 w-32 object-cover rounded-md border border-[#e8e3d5]"/>
                                </div>
                                <p v-if="errors.cover_image" class="mt-1 text-sm text-red-600">{{
                                        errors.cover_image
                                    }}</p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-[#2c3e50] mb-2">Description</label>
                                <textarea
                                    v-model="form.description"
                                    id="description"
                                    rows="7"
                                    placeholder="Enter a brief description of the book"
                                    class="w-full rounded-lg border-[#e8e3d5] shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 transition duration-300"
                                ></textarea>
                                <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{
                                        errors.description
                                    }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional fields -->
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- ISBN -->
                        <div>
                            <label for="isbn" class="block text-sm font-medium text-[#2c3e50] mb-2">ISBN</label>
                            <input
                                v-model="form.isbn"
                                type="text"
                                id="isbn"
                                placeholder="e.g. 978-3-16-148410-0"
                                class="w-full rounded-lg border-[#e8e3d5] shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 transition duration-300"
                                maxlength="17"
                            />
                            <p v-if="errors.isbn" class="mt-1 text-sm text-red-600">{{ errors.isbn }}</p>
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

                    <!-- Submit buttons -->
                    <div class="mt-10 flex justify-between items-center border-t border-[#e8e3d5] pt-6">
                        <p class="text-sm text-gray-500">* Required fields</p>
                        <div class="flex space-x-4">
                            <button
                                type="button"
                                class="px-6 py-3 bg-white border border-[#e8e3d5] rounded-lg text-sm font-medium text-[#2c3e50] hover:bg-[#f9f7f2] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-300"
                                @click="resetForm"
                            >
                                Reset Form
                            </button>
                            <button
                                type="submit"
                                class="px-8 py-3 bg-gradient-to-r from-[#2c3e50] to-[#8b5a2b] rounded-lg text-sm font-medium text-white hover:from-[#34495e] hover:to-[#9c6b3a] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 shadow-md transition-all duration-300"
                                :disabled="processing"
                            >
                                <span v-if="processing">Processing...</span>
                                <span v-else>Create Book</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </main>

        <AppFooter/>
    </div>
</template>

<script setup>
import {reactive, ref} from 'vue';
import {router, Link, usePage} from '@inertiajs/vue3';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

const imagePreview = ref(null);
const processing = ref(false);
const errors = reactive({});

const form = reactive({
    title: '',
    author: '',
    description: '',
    publication_year: '',
    cover_image: null,
    genre_id: '',
    isbn: '',
    status: 'available'
});

const props = defineProps({
    genres: {
        type: Object,
        default: () => ({})
    },
    flash: {
        type: Object,
        default: () => ({})
    }
});

const page = usePage();
const flash = reactive({
    success: page.props.flash?.success || null,
    error: page.props.flash?.error || null
});

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Validate file type and size
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (!validTypes.includes(file.type)) {
            errors.cover_image = 'Invalid file type. Please upload an image (JPEG, PNG, JPG, GIF, or WEBP).';
            return;
        }

        if (file.size > maxSize) {
            errors.cover_image = 'File size too large. Maximum 2MB allowed.';
            return;
        }

        form.cover_image = file;
        delete errors.cover_image;

        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const resetForm = () => {
    Object.keys(form).forEach(key => {
        if (key === 'status') {
            form[key] = 'available';
        } else {
            form[key] = '';
        }
    });
    imagePreview.value = null;
    Object.keys(errors).forEach(key => delete errors[key]);
};

const submitForm = () => {
    processing.value = true;
    Object.keys(errors).forEach(key => delete errors[key]);

    router.post('/books', form, {
        forceFormData: true,
        onSuccess: () => {
            resetForm();
        },
        onError: (err) => {
            Object.assign(errors, err);
        },
        onFinish: () => {
            processing.value = false;
        }
    });
};
</script>
