<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { debounce } from '@/format';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import Icon from '@/Components/Icon.vue';
import Pagination from '@/Components/Pagination.vue';
import { money } from '@/format';

const props = defineProps({ leads: Object, filters: Object, stats: Object });

const q = ref(props.filters.q ?? '');
const status = ref(props.filters.status ?? '');

const statusColors = { new: 'brand', contacted: 'amber', qualified: 'emerald', unqualified: 'rose', converted: 'violet' };

watch([q, status], debounce(() => {
    router.get(route('crm.leads.index'), { q: q.value, status: status.value }, { preserveState: true, replace: true });
}, 300));

function scoreColor(s) { return s >= 70 ? 'emerald' : s >= 40 ? 'amber' : 'slate'; }

function destroy(lead) {
    if (confirm(`Delete lead "${lead.name}"?`)) {
        router.delete(route('crm.leads.destroy', lead.id), { preserveScroll: true });
    }
}
function convert(lead) {
    if (confirm(`Convert "${lead.name}" into a client?`)) {
        router.post(route('crm.leads.convert', lead.id));
    }
}
</script>

<template>
    <Head title="Leads" />
    <AppLayout>
        <template #title>Leads</template>
        <template #subtitle>{{ stats.new }} new · {{ stats.qualified }} qualified · {{ stats.converted }} converted</template>
        <template #actions>
            <Link :href="route('crm.leads.create')" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
                <Icon name="plus" class="h-4 w-4" /> New lead
            </Link>
        </template>

        <Card>
            <div class="mb-4 flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[200px]">
                    <Icon name="search" class="pointer-events-none absolute left-3 top-2.5 h-5 w-5 text-slate-400" />
                    <input v-model="q" placeholder="Search leads..." class="w-full rounded-xl border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" />
                </div>
                <select v-model="status" class="rounded-xl border-slate-300 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                    <option value="">All statuses</option>
                    <option v-for="s in ['new','contacted','qualified','unqualified','converted']" :key="s" :value="s">{{ s }}</option>
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400 dark:border-slate-800">
                            <th class="py-3 pr-4 font-medium">Name</th>
                            <th class="py-3 pr-4 font-medium">Source</th>
                            <th class="py-3 pr-4 font-medium">Status</th>
                            <th class="py-3 pr-4 font-medium">AI Score</th>
                            <th class="py-3 pr-4 font-medium">Est. value</th>
                            <th class="py-3 pr-4 font-medium">Owner</th>
                            <th class="py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-for="lead in leads.data" :key="lead.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="py-3 pr-4">
                                <Link :href="route('crm.leads.show', lead.id)" class="font-medium text-slate-800 hover:text-brand-600 dark:text-slate-200">{{ lead.name }}</Link>
                                <p class="text-xs text-slate-400">{{ lead.company }}</p>
                            </td>
                            <td class="py-3 pr-4 text-slate-500">{{ lead.source }}</td>
                            <td class="py-3 pr-4"><Badge :color="statusColors[lead.status]">{{ lead.status }}</Badge></td>
                            <td class="py-3 pr-4"><Badge :color="scoreColor(lead.score)">{{ lead.score }}</Badge></td>
                            <td class="py-3 pr-4 text-slate-600 dark:text-slate-300">{{ money(lead.estimated_value) }}</td>
                            <td class="py-3 pr-4 text-slate-500">{{ lead.owner?.name ?? '—' }}</td>
                            <td class="py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button v-if="lead.status !== 'converted'" @click="convert(lead)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-emerald-600 dark:hover:bg-slate-700" title="Convert to client"><Icon name="arrow" class="h-4 w-4" /></button>
                                    <Link :href="route('crm.leads.edit', lead.id)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-brand-600 dark:hover:bg-slate-700"><Icon name="pencil" class="h-4 w-4" /></Link>
                                    <button @click="destroy(lead)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-rose-600 dark:hover:bg-slate-700"><Icon name="trash" class="h-4 w-4" /></button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!leads.data.length"><td colspan="7" class="py-12 text-center text-slate-400">No leads found.</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <p class="text-xs text-slate-400">{{ leads.total }} leads</p>
                <Pagination :links="leads.links" />
            </div>
        </Card>
    </AppLayout>
</template>
