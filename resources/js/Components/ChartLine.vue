<script setup>
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import {
    Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement,
    Filler, Tooltip,
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Filler, Tooltip);

const props = defineProps({
    labels: { type: Array, default: () => [] },
    values: { type: Array, default: () => [] },
    label: { type: String, default: 'Series' },
});

const data = computed(() => ({
    labels: props.labels,
    datasets: [{
        label: props.label,
        data: props.values,
        borderColor: '#3563ff',
        backgroundColor: 'rgba(53, 99, 255, 0.12)',
        fill: true,
        tension: 0.4,
        pointRadius: 3,
        pointBackgroundColor: '#3563ff',
    }],
}));

const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
        y: { grid: { color: 'rgba(148,163,184,0.15)' }, ticks: { color: '#94a3b8' }, beginAtZero: true },
    },
};
</script>

<template>
    <div class="h-64"><Line :data="data" :options="options" /></div>
</template>
