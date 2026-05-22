<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import Card from '@/Components/Card.vue';
import ChartLine from '@/Components/ChartLine.vue';
import ChartDoughnut from '@/Components/ChartDoughnut.vue';
import Badge from '@/Components/Badge.vue';
import Icon from '@/Components/Icon.vue';
import { money, relative } from '@/format';

const props = defineProps({
    kpis: Object,
    revenueSeries: Array,
    leadsBySource: Object,
    pipelineByStage: Object,
    recentActivities: Array,
    currency: { type: String, default: 'USD' },
});

const typeColors = { note: 'slate', call: 'brand', email: 'violet', meeting: 'amber', task: 'emerald' };
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout>
        <template #title>Welcome back 👋</template>
        <template #subtitle>Here's what's happening across your business today.</template>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard label="Total Leads" :value="kpis.leads_total" icon="users" accent="brand" :trend="`${kpis.leads_new} new this week`" />
            <StatCard label="Clients" :value="kpis.clients_total" icon="id-badge" accent="violet" />
            <StatCard label="Open Pipeline" :value="money(kpis.pipeline_value, currency)" icon="chart" accent="amber" :trend="`${kpis.deals_open} open deals`" />
            <StatCard label="Won (this month)" :value="money(kpis.won_this_month, currency)" icon="banknotes" accent="emerald" />
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <Card class="lg:col-span-2" title="Revenue (won deals)" subtitle="Last 6 months">
                <ChartLine
                    :labels="revenueSeries.map(p => p.label)"
                    :values="revenueSeries.map(p => p.value)"
                    label="Revenue"
                />
            </Card>
            <Card title="Leads by source">
                <ChartDoughnut :dataset="leadsBySource" />
            </Card>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <Card title="Pipeline by stage" subtitle="Open deal value">
                <ChartDoughnut :dataset="pipelineByStage" />
            </Card>
            <Card class="lg:col-span-2" title="Recent activity">
                <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                    <li v-for="a in recentActivities" :key="a.id" class="flex items-center gap-3 py-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-500 dark:bg-slate-800">
                            <Icon name="bell" class="h-4 w-4" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-800 dark:text-slate-200">{{ a.title || a.type }}</p>
                            <p class="truncate text-xs text-slate-400">{{ a.user?.name }} · {{ relative(a.created_at) }}</p>
                        </div>
                        <Badge :color="typeColors[a.type] || 'slate'">{{ a.type }}</Badge>
                    </li>
                    <li v-if="!recentActivities.length" class="py-8 text-center text-sm text-slate-400">No activity yet.</li>
                </ul>
            </Card>
        </div>
    </AppLayout>
</template>
