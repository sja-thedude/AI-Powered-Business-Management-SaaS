<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import Icon from '@/Components/Icon.vue';

// Generic landing for modules whose schema + API exist and whose UI is being
// built out. Each module controller passes its name, icon and live record
// counts so this page always shows real, tenant-scoped data.
defineProps({
    module: Object,        // { name, icon, description, status }
    stats: { type: Array, default: () => [] }, // [{ label, value, icon, accent }]
    tables: { type: Array, default: () => [] }, // schema table names shipped
});
</script>

<template>
    <Head :title="module.name" />
    <AppLayout>
        <template #title>
            <span class="inline-flex items-center gap-2">{{ module.name }} <Badge color="amber">scaffold</Badge></span>
        </template>
        <template #subtitle>{{ module.description }}</template>

        <div v-if="stats.length" class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <StatCard v-for="s in stats" :key="s.label" :label="s.label" :value="s.value" :icon="s.icon || module.icon" :accent="s.accent || 'brand'" />
        </div>

        <Card>
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-300">
                    <Icon :name="module.icon" class="h-8 w-8" />
                </span>
                <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">{{ module.name }} is ready to build on</h3>
                <p class="mt-2 max-w-md text-sm text-slate-500 dark:text-slate-400">
                    The database schema, Eloquent models and tenant-scoped API for this module are in place.
                    The full UI follows the same pattern as the CRM module — drop in controllers and pages and it lights up.
                </p>
                <div v-if="tables.length" class="mt-6 flex flex-wrap justify-center gap-2">
                    <code v-for="t in tables" :key="t" class="rounded-md bg-slate-100 px-2 py-1 text-xs text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ t }}</code>
                </div>
            </div>
        </Card>
    </AppLayout>
</template>
