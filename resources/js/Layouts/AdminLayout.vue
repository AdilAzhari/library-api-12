<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Sidebar -->
        <AdminSidebar :is-open="sidebarOpen" @close="sidebarOpen = false" />

        <!-- Mobile sidebar toggle -->
        <div class="lg:hidden fixed z-20 inset-0 flex-none h-full bg-black bg-opacity-25 w-full"
             v-show="sidebarOpen" @click="sidebarOpen = false"></div>

        <!-- Content Area -->
        <div class="lg:pl-72">
            <!-- Header -->
            <AdminHeader @toggle-sidebar="sidebarOpen = !sidebarOpen" />

            <!-- Main Content -->
            <main class="py-6 px-4 sm:px-6 lg:px-8">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import AdminSidebar from '@/Components/Admin/Sidebar.vue';
import AdminHeader from '@/Components/Admin/Header.vue';

const sidebarOpen = ref(false);
</script>
