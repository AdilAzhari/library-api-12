<template>
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Reservation Management</h1>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input v-model="search" type="text" placeholder="Search reservations..."
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                        </div>
                    </div>
                    <div class="ml-4">
                        <select v-model="statusFilter"
                                class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm rounded-md">
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                            <option value="fulfilled">Fulfilled</option>
                            <option value="canceled">Canceled</option>
                            <option value="">All</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <colgroup>
                        <col class="w-1/4"> <!-- Book -->
                        <col class="w-1/6"> <!-- User -->
                        <col class="w-1/6"> <!-- Reserved -->
                        <col class="w-1/6"> <!-- Expires -->
                        <col class="w-1/6"> <!-- Status -->
                        <col class="w-1/6"> <!-- Actions -->
                    </colgroup>
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Reserved
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Expires
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="reservation in reservations.data" :key="reservation.id">
                        <td class="px-6 py-4
                        ">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img v-if="reservation.book.cover_image_url" :src="reservation.book.cover_image_url"
                                         alt="Book cover"
                                         class="h-10 w-10 rounded object-cover">
                                    <div v-else class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ reservation.book.title }}</div>
                                    <div class="text-sm text-gray-500">{{ reservation.book.author }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ reservation.user.name }}</div>
                            <div class="text-sm text-gray-500">{{ reservation.user.email }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ new Date(reservation.reserved_at).toLocaleDateString() }}
                        </td>
                        <!-- Expires Column -->
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ new Date(reservation.expires_at).toLocaleDateString() }}
                        </td>

                        <!-- Status Column -->
                        <td class="px-6 py-4">
    <span :class="{
        'bg-green-100 text-green-800': reservation.is_active,
        'bg-yellow-100 text-yellow-800': reservation.is_expired,
        'bg-blue-100 text-blue-800': reservation.is_fulfilled,
        'bg-red-100 text-red-800': reservation.is_canceled
    }" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
        {{
            reservation.is_fulfilled ? 'fulfilled' :
                reservation.is_canceled ? 'canceled' :
                    reservation.is_expired ? 'expired' : 'active'
        }}
    </span>
                        </td>

                        <!-- Actions Column -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <button v-if="reservation.is_active"
                                        @click="openFulfillModal(reservation)"
                                        class="text-blue-600 hover:text-blue-900">
                                    Fulfill
                                </button>
                                <button v-if="reservation.is_active"
                                        @click="openCancelModal(reservation)"
                                        class="text-red-600 hover:text-red-900">
                                    Cancel
                                </button>
                            </div>
                        </td>
                        <!-- Actions Column -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <!-- Fulfill Button - Only show for active reservations -->
                                <button
                                    v-if="!reservation.is_fulfilled && !reservation.is_canceled && !reservation.is_expired"
                                    @click="openFulfillModal(reservation)"
                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                >
                                    Fulfill
                                </button>

                                <!-- Cancel Button - Only show for active reservations -->
                                <button
                                    v-if="!reservation.is_fulfilled && !reservation.is_canceled && !reservation.is_expired"
                                    @click="openCancelModal(reservation)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    Cancel
                                </button>

                                <!-- View Fulfillment Button - Show for fulfilled reservations -->
                                <Link
                                    v-if="reservation.is_fulfilled"
                                    :href="`/admin/borrowings/${reservation.fulfilled_by_borrow_id}`"
                                    class="text-green-600 hover:text-green-900"
                                >
                                    View Fulfillment
                                </Link>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <Pagination :links="reservations.links"
                            :from="reservations.from"
                            :to="reservations.to"
                            :total="reservations.total"
                            @navigate="handleNavigation"/>
            </div>
        </div>

        <!-- Fulfillment Modal -->
        <ConfirmationModal :show="showFulfillModal" @close="showFulfillModal = false">
            <template #title>Fulfill Reservation</template>
            <template #content>
                <p class="mb-4">Are you sure you want to fulfill this reservation for
                    "{{ selectedReservation?.book.title }}"?</p>
                <div class="space-y-4">
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                        <input v-model="dueDate" type="date" id="due_date"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                    </div>
                </div>
            </template>
            <template #footer>
                <button @click="showFulfillModal = false" type="button"
                        class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                    Cancel
                </button>
                <button @click="fulfillReservation" type="button"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#2c3e50] hover:bg-[#34495e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                    Confirm Fulfillment
                </button>
            </template>
        </ConfirmationModal>

        <!-- Cancel Confirmation Modal -->
        <ConfirmationModal :show="showCancelModal" @close="showCancelModal = false">
            <template #title>Cancel Reservation</template>
            <template #content>Are you sure you want to cancel this reservation for "{{
                    selectedReservation?.book.title
                }}"?
            </template>
            <template #footer>
                <button @click="showCancelModal = false" type="button"
                        class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2c3e50]">
                    No, Keep It
                </button>
                <button @click="cancelReservation" type="button"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Yes, Cancel
                </button>
            </template>
        </ConfirmationModal>
    </AdminLayout>
</template>
<script setup> import {ref, computed, watch} from 'vue';
import {Link, router} from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import {debounce} from 'lodash';

const props = defineProps({reservations: Object, filters: Object});
const search = ref(props.filters.search);
const statusFilter = ref(props.filters.status);
const showFulfillModal = ref(false);
const showCancelModal = ref(false);
const selectedReservation = ref(null);
const dueDate = ref(''); // Calculate default due date (2 weeks from now)
const defaultDueDate = new Date();
defaultDueDate.setDate(defaultDueDate.getDate() + 14);
dueDate.value = defaultDueDate.toISOString().split('T')[0];

watch([search, statusFilter], debounce(([searchValue, statusValue]) => {
    router.get('/admin/reservations',
        {
            search: searchValue,
            status: statusValue
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ['reservations', 'filters']
        });
}, 300));
const openFulfillModal = (reservation) => {
    selectedReservation.value = reservation;
    showFulfillModal.value = true;
};

const fulfillReservation = () => {
    if (!selectedReservation.value) return;

    router.post(`/admin/reservations/${selectedReservation.value.id}/fulfill`,
        {due_date: dueDate.value},
        {
            preserveScroll: true,
            onSuccess: () => {
                showFulfillModal.value = false;
                selectedReservation.value = null;
            }
        }
    );
};

const openCancelModal = (reservation) => {
    selectedReservation.value = reservation;
    showCancelModal.value = true;
};

const cancelReservation = () => {
    if (!selectedReservation.value) return;

    router.post(`/admin/reservations/${selectedReservation.value.id}/cancel`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                showCancelModal.value = false;
                selectedReservation.value = null;
            }
        }
    );
};
const handleNavigation = async (url) => {
    if (!url) return;

    await router.visit(url, {
        preserveState: true,
        preserveScroll: true,
        only: ['reservations', 'filters'],
        onSuccess: () => {
            // Force update if needed
            router.reload({only: ['filters', 'reservations']});
        }
    });
};
</script>
