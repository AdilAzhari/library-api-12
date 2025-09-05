<template>
    <AdminLayout>
        <div class="space-y-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <StatsCard title="Total Books" :value="stats.total_books" icon="book" color="blue"/>
                <StatsCard title="Active Borrowings" :value="stats.active_borrowings" icon="borrow" color="green"/>
                <StatsCard title="Overdue Books" :value="stats.overdue_books" icon="alert" color="red"/>
                <StatsCard title="Active Reservations" :value="stats.active_reservations" icon="reservation"
                           color="amber"/>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Borrowings Last 6 Months</h2>
                    <BorrowingsChart :data="charts.borrowings || {}"/>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Popular Genres</h2>
                    <GenresChart :data="charts.genres || []"/>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h2>
                <ActivityFeed :activities="recentActivities"/>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatsCard from '@/Components/Admin/StatsCard.vue';
import BorrowingsChart from '@/Components/Admin/Charts/BorrowingsChart.vue';
import GenresChart from '@/Components/Admin/Charts/GenresChart.vue';
import ActivityFeed from '@/Components/Admin/ActivityFeed.vue';

defineProps({
    stats: {
        type: Object,
        required: true
    },
    charts: {
        type: Object,
        required: true
    },
    recentActivities: {
        type: Array,
        required: true
    }
});
</script>
