<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import Pagination from '@/Components/Pagination.vue';
import { relative } from '@/format';

defineProps({ logs: Object });

const eventColors = { created: 'emerald', updated: 'amber', deleted: 'rose', restored: 'brand' };
function modelName(type) { return type.split('\\').pop(); }
</script>

<template>
    <Head title="Audit log" />
    <AppLayout>
        <template #title>Audit log</template>
        <template #subtitle>An immutable trail of every change in your workspace.</template>

        <div class="mb-6 flex gap-2">
            <Link :href="route('settings.index')" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">Workspace</Link>
            <Link :href="route('settings.team')" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">Team</Link>
            <Link :href="route('settings.audit')" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white">Audit log</Link>
        </div>

        <Card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400 dark:border-slate-800">
                            <th class="py-3 pr-4 font-medium">Event</th>
                            <th class="py-3 pr-4 font-medium">Record</th>
                            <th class="py-3 pr-4 font-medium">User</th>
                            <th class="py-3 pr-4 font-medium">IP</th>
                            <th class="py-3 font-medium">When</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-for="log in logs.data" :key="log.id">
                            <td class="py-3 pr-4"><Badge :color="eventColors[log.event]">{{ log.event }}</Badge></td>
                            <td class="py-3 pr-4 text-slate-700 dark:text-slate-300">{{ modelName(log.auditable_type) }} #{{ log.auditable_id }}</td>
                            <td class="py-3 pr-4 text-slate-500">{{ log.user?.name ?? 'System' }}</td>
                            <td class="py-3 pr-4 text-slate-400">{{ log.ip_address ?? '—' }}</td>
                            <td class="py-3 text-slate-500">{{ relative(log.created_at) }}</td>
                        </tr>
                        <tr v-if="!logs.data.length"><td colspan="5" class="py-12 text-center text-slate-400">No audit entries yet.</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-end"><Pagination :links="logs.links" /></div>
        </Card>
    </AppLayout>
</template>
