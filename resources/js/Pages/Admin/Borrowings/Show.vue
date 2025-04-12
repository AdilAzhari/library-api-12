<template>
    <AdminLayout title="Borrowing Details">
        <div class="max-w-3xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Borrowing Details</h2>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Book Information -->
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 h-20 w-20">
                            <img v-if="borrow.book.cover_image_url" :src="borrow.book.cover_image_url"
                                 class="h-full w-full rounded object-cover">
                            <div v-else class="h-full w-full rounded bg-gray-200 flex items-center justify-center">
                                <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ borrow.book.title }}</h3>
                            <p class="text-sm text-gray-500">{{ borrow.book.author }}</p>
                        </div>
                    </div>

                    <!-- User Information -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Borrowed By</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ borrow.user.name }}</p>
                        <p class="text-sm text-gray-500">{{ borrow.user.email }}</p>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Borrowed Date</h4>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ new Date(borrow.borrowed_at).toLocaleDateString() }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Due Date</h4>
                            <p class="mt-1 text-sm text-gray-900">{{
                                    new Date(borrow.due_date).toLocaleDateString()
                                }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Returned Date</h4>
                            <p class="mt-1 text-sm text-gray-900">
                                {{
                                    borrow.returned_at ? new Date(borrow.returned_at).toLocaleDateString() : 'Not returned'
                                }}
                            </p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <!-- Status Indicator -->
                        <div class="h-2" :class="{
    'bg-green-500': borrow.status === 'returned',
    'bg-blue-500': borrow.status === 'active',
    'bg-red-500': borrow.status === 'overdue'
}"></div>

                        <!-- Status Badge -->
                        <span class="inline-block px-3 py-1 text-sm font-medium rounded-full"
                              :class="{
        'bg-green-100 text-green-800': borrow.status === 'returned',
        'bg-blue-100 text-blue-800': borrow.status === 'active',
        'bg-red-100 text-red-800': borrow.status === 'overdue'
    }">
    {{
                                borrow.status === 'returned' ? 'Returned' :
                                    borrow.status === 'overdue' ? 'Overdue' : 'Active'
                            }}
</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="px-6 py-3 bg-gray-50 text-right border-t border-gray-200">
                    <Link :href="route('admin.borrowings.index')"
                          class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#2c3e50] hover:bg-[#34495e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                        Back to Borrowings
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {Link} from '@inertiajs/vue3';

const props = defineProps({
    borrow: Object,
    status: String
});
</script>
