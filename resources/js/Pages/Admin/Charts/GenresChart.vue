<template>
    <div class="chart-container">
        <Pie
            v-if="chartData && chartData.labels && chartData.labels.length"
            :data="chartData"
            :options="options"
            :height="300"
        />
        <div v-else class="text-gray-500 text-center p-4">
            No genre data available
        </div>
    </div>
</template>

<script setup>
import { Pie } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale } from 'chart.js';
import { computed } from 'vue';

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

const props = defineProps({
    data: {
        type: Array,
        required: true
    }
});

const chartData = computed(() => {
    if (!props.data || !props.data.length) return null;

    return {
        labels: props.data.map(item => item.name),
        datasets: [{
            data: props.data.map(item => item.count),
            backgroundColor: [
                '#2c3e50',
                '#3498db',
                '#e74c3c',
                '#2ecc71',
                '#f39c12'
            ],
            borderWidth: 1
        }]
    };
});

const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right',
        },
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
