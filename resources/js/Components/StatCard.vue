<script setup>
import Icon from './Icon.vue';

defineProps({
    label: String,
    value: [String, Number],
    icon: { type: String, default: 'chart' },
    trend: { type: String, default: null },
    trendUp: { type: Boolean, default: true },
    accent: { type: String, default: 'brand' }, // brand|emerald|amber|rose
});

const accents = {
    brand: 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-300',
    emerald: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300',
    amber: 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300',
    rose: 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300',
};
</script>

<template>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ label }}</p>
                <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ value }}</p>
            </div>
            <span :class="['flex h-10 w-10 items-center justify-center rounded-xl', accents[accent]]">
                <Icon :name="icon" class="h-5 w-5" />
            </span>
        </div>
        <p v-if="trend" class="mt-3 text-xs font-medium" :class="trendUp ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
            {{ trendUp ? '▲' : '▼' }} {{ trend }}
        </p>
    </div>
</template>
