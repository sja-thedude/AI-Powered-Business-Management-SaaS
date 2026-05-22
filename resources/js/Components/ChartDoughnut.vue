<script setup>
import { computed } from 'vue';
import { Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps({
    // A { label: value } map.
    dataset: { type: Object, default: () => ({}) },
});

const palette = ['#3563ff', '#22c55e', '#f59e0b', '#a855f7', '#ef4444', '#06b6d4', '#94a3b8'];

const data = computed(() => ({
    labels: Object.keys(props.dataset),
    datasets: [{
        data: Object.values(props.dataset),
        backgroundColor: palette,
        borderWidth: 0,
    }],
}));

const options = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '65%',
    plugins: {
        legend: { position: 'bottom', labels: { color: '#94a3b8', boxWidth: 12, padding: 14 } },
    },
};
</script>

<template>
    <div class="h-64">
        <Doughnut v-if="Object.keys(dataset).length" :data="data" :options="options" />
        <p v-else class="flex h-full items-center justify-center text-sm text-slate-400">No data yet</p>
    </div>
</template>
