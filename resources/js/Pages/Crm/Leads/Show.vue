<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import ActivityTimeline from '@/Components/ActivityTimeline.vue';
import { money } from '@/format';

const props = defineProps({ lead: Object, activities: Array });

const statusColors = { new: 'brand', contacted: 'amber', qualified: 'emerald', unqualified: 'rose', converted: 'violet' };

function convert() {
    if (confirm('Convert this lead into a client?')) router.post(route('crm.leads.convert', props.lead.id));
}
</script>

<template>
    <Head :title="lead.name" />
    <AppLayout>
        <template #title>{{ lead.name }}</template>
        <template #subtitle>{{ lead.company }}</template>
        <template #actions>
            <div class="flex gap-2">
                <button v-if="lead.status !== 'converted'" @click="convert" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Convert to client</button>
                <Link :href="route('crm.leads.edit', lead.id)" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200">Edit</Link>
            </div>
        </template>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <Card title="Details" class="lg:col-span-1">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-400">Status</dt><dd><Badge :color="statusColors[lead.status]">{{ lead.status }}</Badge></dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">AI Score</dt><dd class="font-semibold text-slate-800 dark:text-slate-200">{{ lead.score }}/100</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">Source</dt><dd class="text-slate-700 dark:text-slate-300">{{ lead.source }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">Est. value</dt><dd class="text-slate-700 dark:text-slate-300">{{ money(lead.estimated_value) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">Email</dt><dd class="text-slate-700 dark:text-slate-300">{{ lead.email ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">Phone</dt><dd class="text-slate-700 dark:text-slate-300">{{ lead.phone ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">Owner</dt><dd class="text-slate-700 dark:text-slate-300">{{ lead.owner?.name ?? '—' }}</dd></div>
                </dl>
                <div v-if="lead.notes" class="mt-4 rounded-xl bg-slate-50 p-3 text-sm text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ lead.notes }}</div>
            </Card>

            <Card title="Communication history" class="lg:col-span-2">
                <ActivityTimeline subject-type="lead" :subject-id="lead.id" :activities="activities" />
            </Card>
        </div>
    </AppLayout>
</template>
