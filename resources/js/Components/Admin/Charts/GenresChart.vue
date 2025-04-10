<template>
    <div>
        <apexchart
            type="pie"
            height="350"
            :options="chartOptions"
            :series="series"
        />
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    data: Array
});

const series = computed(() => props.data.map(item => item.count));
const labels = computed(() => props.data.map(item => item.name));

const chartOptions = computed(() => ({
    chart: {
        type: 'pie',
    },
    labels: labels.value,
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                width: 200
            },
            legend: {
                position: 'bottom'
            }
        }
    }],
    colors: ['#2c3e50', '#3498db', '#9b59b6', '#1abc9c', '#f1c40f', '#e74c3c'],
    legend: {
        position: 'right'
    },
    tooltip: {
        y: {
            formatter: function(value) {
                return value + " borrowings";
            }
        }
    }
}));
</script>
