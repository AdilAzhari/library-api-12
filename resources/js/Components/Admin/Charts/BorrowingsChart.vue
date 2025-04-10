<template>
    <div>
        <apexchart
            type="bar"
            height="350"
            :options="chartOptions"
            :series="series"
        />
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    data: Object
});

const series = computed(() => props.data.datasets.map(dataset => ({
    name: dataset.label,
    data: dataset.data
})));

const chartOptions = computed(() => ({
    chart: {
        type: 'bar',
        toolbar: {
            show: true,
            tools: {
                download: true
            }
        }
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '55%',
            endingShape: 'rounded'
        },
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
    },
    xaxis: {
        categories: props.data.labels
    },
    yaxis: {
        title: {
            text: 'Count'
        }
    },
    fill: {
        opacity: 1
    },
    colors: props.data.datasets.map(d => d.backgroundColor),
    tooltip: {
        y: {
            formatter: function (val) {
                return val
            }
        }
    }
}));
</script>
