<template>
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Borrowing Reports</h1>
            <div class="flex space-x-2">
                <button @click="exportToCSV"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Export CSV
                </button>
                <button @click="exportToPDF"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Export PDF
                </button>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                    <div class="flex-1">
                        <label for="date_range" class="block text-sm font-medium text-gray-700">Date Range</label>
                        <select v-model="dateRange" id="date_range"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>

                    <div v-if="dateRange === 'custom'" class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input v-model="startDate" type="date" id="start_date"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input v-model="endDate" type="date" id="end_date"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2c3e50] focus:border-[#2c3e50] sm:text-sm">
                        </div>
                    </div>

                    <div class="flex items-end">
                        <button @click="generateReport" type="button"
                                class="bg-[#2c3e50] hover:bg-[#34495e] text-white px-4 py-2 rounded-md text-sm font-medium">
                            Generate Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Borrowings Overview</h2>
                <BorrowingsChart :data="reportData.chart"/>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Top Borrowed Books</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Book
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Borrow Count
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(book, index) in reportData.topBooks" :key="index">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ book.title }}</div>
                                <div class="text-sm text-gray-500">{{ book.author }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ book.borrow_count }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-6 bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Detailed Borrowings</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
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
                            Borrowed Date
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due
                            Date
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="borrowing in reportData.borrowings" :key="borrowing.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ borrowing.book.title }}</div>
                            <div class="text-sm text-gray-500">{{ borrowing.book.author }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ borrowing.user.name }}</div>
                            <div class="text-sm text-gray-500">{{ borrowing.user.email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ new Date(borrowing.borrowed_at).toLocaleDateString() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ new Date(borrowing.due_date).toLocaleDateString() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                <span :class="{
                  'bg-green-100 text-green-800': borrowing.status === 'active',
                  'bg-red-100 text-red-800': borrowing.status === 'overdue',
                  'bg-gray-100 text-gray-800': borrowing.status === 'returned'
                }" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ borrowing.status }}
                </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import {ref, computed} from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BorrowingsChart from '@/Components/Admin/Charts/BorrowingsChart.vue';

const dateRange = ref('month');
const startDate = ref('');
const endDate = ref('');

// Sample data - in a real app this would come from the backend
const reportData = ref({
    chart: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        datasets: [
            {
                label: 'Borrowings',
                data: [12, 19, 3, 5, 2, 3, 15],
                backgroundColor: '#2c3e50'
            },
            {
                label: 'Returns',
                data: [8, 15, 5, 7, 4, 6, 12],
                backgroundColor: '#3498db'
            }
        ]
    },
    topBooks: [
        {title: 'The Great Gatsby', author: 'F. Scott Fitzgerald', borrow_count: 24},
        {title: 'To Kill a Mockingbird', author: 'Harper Lee', borrow_count: 18},
        {title: '1984', author: 'George Orwell', borrow_count: 15},
        {title: 'Pride and Prejudice', author: 'Jane Austen', borrow_count: 12},
        {title: 'The Hobbit', author: 'J.R.R. Tolkien', borrow_count: 10}
    ],
    borrowings: [
        {
            id: 1,
            book: {title: 'The Great Gatsby', author: 'F. Scott Fitzgerald'},
            user: {name: 'John Doe', email: 'john@example.com'},
            borrowed_at: '2023-05-15',
            due_date: '2023-05-29',
            status: 'returned'
        },
        // More sample data...
    ]
});

const generateReport = () => {
    // In a real app, this would make an API call to get report data
    console.log('Generating report for:', dateRange.value, startDate.value, endDate.value);
};

const exportToCSV = () => {
    // CSV export logic
    console.log('Exporting to CSV');
};

const exportToPDF = () => {
    // PDF export logic
    console.log('Exporting to PDF');
};
</script>
