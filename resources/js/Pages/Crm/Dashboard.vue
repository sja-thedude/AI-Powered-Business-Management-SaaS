<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import { money, date } from '@/format';

defineProps({ kpis: Object, topLeads: Array, closingSoon: Array });

function scoreColor(s) {
    return s >= 70 ? 'emerald' : s >= 40 ? 'amber' : 'slate';
}
</script>

<template>
    <Head title="CRM" />
    <AppLayout>
        <template #title>CRM Overview</template>
        <template #subtitle>Pipeline health, hot leads and deals closing soon.</template>
        <template #actions>
            <Link :href="route('crm.deals.board')" class="rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Open Pipeline</Link>
        </template>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-6">
            <StatCard label="Leads" :value="kpis.leads_total" icon="users" accent="brand" />
            <StatCard label="Hot leads" :value="kpis.leads_hot" icon="sparkles" accent="rose" />
            <StatCard label="Open deals" :value="kpis.open_deals" icon="rectangle-stack" accent="amber" />
            <StatCard label="Pipeline" :value="money(kpis.pipeline)" icon="chart" accent="violet" />
            <StatCard label="Won" :value="money(kpis.won_value)" icon="banknotes" accent="emerald" />
            <StatCard label="Win rate" :value="`${kpis.win_rate}%`" icon="check" accent="emerald" />
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <Card title="Top AI-scored leads">
                <template #actions>
                    <Link :href="route('crm.leads.index')" class="text-sm font-medium text-brand-600 hover:underline">View all</Link>
                </template>
                <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                    <li v-for="lead in topLeads" :key="lead.id" class="flex items-center justify-between py-3">
                        <Link :href="route('crm.leads.show', lead.id)" class="min-w-0">
                            <p class="truncate text-sm font-medium text-slate-800 hover:text-brand-600 dark:text-slate-200">{{ lead.name }}</p>
                            <p class="truncate text-xs text-slate-400">{{ lead.company }}</p>
                        </Link>
                        <Badge :color="scoreColor(lead.score)">Score {{ lead.score }}</Badge>
                    </li>
                    <li v-if="!topLeads.length" class="py-8 text-center text-sm text-slate-400">No leads yet.</li>
                </ul>
            </Card>

            <Card title="Deals closing soon">
                <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                    <li v-for="deal in closingSoon" :key="deal.id" class="flex items-center justify-between py-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-slate-800 dark:text-slate-200">{{ deal.title }}</p>
                            <p class="truncate text-xs text-slate-400">{{ deal.client?.name }} · {{ date(deal.expected_close_date) }}</p>
                        </div>
                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ money(deal.value) }}</span>
                    </li>
                    <li v-if="!closingSoon.length" class="py-8 text-center text-sm text-slate-400">Nothing closing soon.</li>
                </ul>
            </Card>
        </div>
    </AppLayout>
</template>
