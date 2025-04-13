<template>
    <div class="chart-container">
        <Bar
            v-if="data && data.labels && data.labels.length"
            :data="data"
            :options="options"
            :height="300"
        />
        <div v-else class="text-gray-500 text-center p-4">
            No borrowing data available
        </div>
    </div>
</template>

<script setup>
import {Bar} from 'vue-chartjs';
import {Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
    data: {
        type: Object,
        required: true
    }
});

const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1
            }
        }
    }
};
</script>

<style>
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}
</style>
