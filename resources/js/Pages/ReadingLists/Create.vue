<template>
    <AuthenticatedLayout>
        <Head title="Create Reading List"/>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <Link href="/reading-lists" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    ← Back to Reading Lists
                </Link>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">Create Reading List</h1>
                <p class="mt-2 text-gray-600">Create a new reading list to organize your favorite books</p>
            </div>

            <form @submit.prevent="submitForm" class="space-y-8">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                    </div>
                    <div class="px-6 py-6 space-y-6">
                        <!-- List Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                List Name *
                            </label>
                            <input 
                                id="name"
                                v-model="form.name" 
                                type="text" 
                                class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-300': form.errors.name }"
                                placeholder="Enter reading list name"
                                required>
                            <p v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea 
                                id="description"
                                v-model="form.description" 
                                rows="3"
                                class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-300': form.errors.description }"
                                placeholder="Describe your reading list (optional)"></textarea>
                            <p v-if="form.errors.description" class="text-red-600 text-sm mt-1">{{ form.errors.description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Appearance Settings -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Appearance</h3>
                    </div>
                    <div class="px-6 py-6 space-y-6">
                        <!-- Color Theme -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Color Theme
                            </label>
                            <div class="grid grid-cols-4 gap-3">
                                <div v-for="color in colorOptions" :key="color" class="relative">
                                    <input 
                                        :id="`color-${color}`"
                                        v-model="form.color_theme" 
                                        :value="color"
                                        type="radio" 
                                        class="sr-only">
                                    <label 
                                        :for="`color-${color}`"
                                        class="flex items-center justify-center w-full h-12 border-2 rounded-lg cursor-pointer hover:bg-gray-50"
                                        :class="form.color_theme === color ? `border-${color}-500 bg-${color}-50` : 'border-gray-200'">
                                        <div 
                                            class="w-6 h-6 rounded-full"
                                            :class="`bg-${color}-500`"></div>
                                        <span class="ml-2 text-sm font-medium capitalize" :class="form.color_theme === color ? `text-${color}-700` : 'text-gray-700'">
                                            {{ color }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Icon Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Icon
                            </label>
                            <div class="grid grid-cols-4 gap-3">
                                <div v-for="icon in iconOptions" :key="icon.value" class="relative">
                                    <input 
                                        :id="`icon-${icon.value}`"
                                        v-model="form.icon" 
                                        :value="icon.value"
                                        type="radio" 
                                        class="sr-only">
                                    <label 
                                        :for="`icon-${icon.value}`"
                                        class="flex flex-col items-center justify-center w-full h-16 border-2 rounded-lg cursor-pointer hover:bg-gray-50"
                                        :class="form.icon === icon.value ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                        <svg class="w-5 h-5 mb-1" :class="form.icon === icon.value ? 'text-blue-600' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                            <path v-html="icon.path"></path>
                                        </svg>
                                        <span class="text-xs font-medium" :class="form.icon === icon.value ? 'text-blue-700' : 'text-gray-700'">
                                            {{ icon.label }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Privacy & Settings -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Privacy & Settings</h3>
                    </div>
                    <div class="px-6 py-6 space-y-6">
                        <!-- Visibility -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                List Visibility
                            </label>
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <input 
                                        id="private"
                                        v-model="form.is_public" 
                                        :value="false"
                                        type="radio" 
                                        class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <div class="ml-3">
                                        <label for="private" class="text-sm font-medium text-gray-700">
                                            Private
                                        </label>
                                        <p class="text-sm text-gray-500">Only you can see this list</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <input 
                                        id="public"
                                        v-model="form.is_public" 
                                        :value="true"
                                        type="radio" 
                                        class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <div class="ml-3">
                                        <label for="public" class="text-sm font-medium text-gray-700">
                                            Public
                                        </label>
                                        <p class="text-sm text-gray-500">Other users can discover and view this list</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Default List -->
                        <div>
                            <div class="flex items-center">
                                <input 
                                    id="is_default"
                                    v-model="form.is_default" 
                                    type="checkbox" 
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_default" class="ml-3 text-sm font-medium text-gray-700">
                                    Set as default list
                                </label>
                            </div>
                            <p class="text-sm text-gray-500 mt-1 ml-7">
                                Books will be automatically added to this list when you save them for later
                            </p>
                        </div>

                        <!-- Allow Comments (for public lists) -->
                        <div v-if="form.is_public">
                            <div class="flex items-center">
                                <input 
                                    id="allow_comments"
                                    v-model="form.allow_comments" 
                                    type="checkbox" 
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="allow_comments" class="ml-3 text-sm font-medium text-gray-700">
                                    Allow comments
                                </label>
                            </div>
                            <p class="text-sm text-gray-500 mt-1 ml-7">
                                Let other users leave comments on your reading list
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4">
                    <Link href="/reading-lists" 
                          class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </Link>
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'Creating...' : 'Create Reading List' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const form = useForm({
    name: '',
    description: '',
    color_theme: 'blue',
    icon: 'list',
    is_public: false,
    is_default: false,
    allow_comments: true,
})

const colorOptions = ['blue', 'green', 'purple', 'red', 'yellow', 'indigo', 'pink', 'gray']

const iconOptions = [
    {
        value: 'list',
        label: 'List',
        path: 'M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z'
    },
    {
        value: 'book',
        label: 'Book',
        path: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
    },
    {
        value: 'star',
        label: 'Star',
        path: 'M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z'
    },
    {
        value: 'heart',
        label: 'Heart',
        path: 'M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z'
    },
    {
        value: 'bookmark',
        label: 'Bookmark',
        path: 'M5 5a2 2 0 012-2h6a2 2 0 012 2v6l-3-2.5L9 11V5z'
    },
    {
        value: 'collection',
        label: 'Collection',
        path: 'M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z'
    },
    {
        value: 'academic',
        label: 'Academic',
        path: 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'
    },
    {
        value: 'lightning',
        label: 'Lightning',
        path: 'M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z'
    }
]

const submitForm = () => {
    form.post('/reading-lists', {
        onSuccess: () => {
            // Success handled by redirect
        },
        onError: (errors) => {
            console.error('Validation errors:', errors)
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