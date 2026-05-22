<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import ActivityTimeline from '@/Components/ActivityTimeline.vue';
import { money } from '@/format';

defineProps({ client: Object, deals: Array, activities: Array });

const statusColors = { active: 'emerald', prospect: 'amber', inactive: 'slate' };
const dealColors = { open: 'brand', won: 'emerald', lost: 'rose' };
</script>

<template>
    <Head :title="client.name" />
    <AppLayout>
        <template #title>{{ client.name }}</template>
        <template #subtitle>{{ client.company }}</template>
        <template #actions>
            <Link :href="route('crm.clients.edit', client.id)" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200">Edit</Link>
        </template>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-1">
                <Card title="Details">
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-400">Status</dt><dd><Badge :color="statusColors[client.status]">{{ client.status }}</Badge></dd></div>
                        <div class="flex justify-between"><dt class="text-slate-400">Email</dt><dd class="text-slate-700 dark:text-slate-300">{{ client.email ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-400">Phone</dt><dd class="text-slate-700 dark:text-slate-300">{{ client.phone ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-400">Website</dt><dd class="text-slate-700 dark:text-slate-300">{{ client.website ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-400">Owner</dt><dd class="text-slate-700 dark:text-slate-300">{{ client.owner?.name ?? '—' }}</dd></div>
                    </dl>
                </Card>
                <Card title="Deals">
                    <ul class="space-y-2">
                        <li v-for="deal in deals" :key="deal.id" class="flex items-center justify-between rounded-xl border border-slate-100 px-3 py-2 dark:border-slate-800">
                            <div>
                                <p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ deal.title }}</p>
                                <p class="text-xs text-slate-400">{{ deal.stage?.name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ money(deal.value) }}</p>
                                <Badge :color="dealColors[deal.status]">{{ deal.status }}</Badge>
                            </div>
                        </li>
                        <li v-if="!deals.length" class="py-4 text-center text-sm text-slate-400">No deals yet.</li>
                    </ul>
                </Card>
            </div>

            <Card title="Communication history" class="lg:col-span-2">
                <ActivityTimeline subject-type="client" :subject-id="client.id" :activities="activities" />
            </Card>
        </div>
    </AppLayout>
</template>
