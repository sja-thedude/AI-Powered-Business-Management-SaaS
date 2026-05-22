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

const props = defineProps({ clients: Object, filters: Object });

const q = ref(props.filters.q ?? '');
const statusColors = { active: 'emerald', prospect: 'amber', inactive: 'slate' };

watch(q, debounce(() => {
    router.get(route('crm.clients.index'), { q: q.value }, { preserveState: true, replace: true });
}, 300));

function destroy(client) {
    if (confirm(`Delete client "${client.name}"?`)) {
        router.delete(route('crm.clients.destroy', client.id), { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Clients" />
    <AppLayout>
        <template #title>Clients</template>
        <template #subtitle>{{ clients.total }} clients in your workspace</template>
        <template #actions>
            <Link :href="route('crm.clients.create')" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
                <Icon name="plus" class="h-4 w-4" /> New client
            </Link>
        </template>

        <Card>
            <div class="relative mb-4 max-w-sm">
                <Icon name="search" class="pointer-events-none absolute left-3 top-2.5 h-5 w-5 text-slate-400" />
                <input v-model="q" placeholder="Search clients..." class="w-full rounded-xl border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" />
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400 dark:border-slate-800">
                            <th class="py-3 pr-4 font-medium">Name</th>
                            <th class="py-3 pr-4 font-medium">Status</th>
                            <th class="py-3 pr-4 font-medium">Deals</th>
                            <th class="py-3 pr-4 font-medium">Owner</th>
                            <th class="py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-for="client in clients.data" :key="client.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="py-3 pr-4">
                                <Link :href="route('crm.clients.show', client.id)" class="font-medium text-slate-800 hover:text-brand-600 dark:text-slate-200">{{ client.name }}</Link>
                                <p class="text-xs text-slate-400">{{ client.company }}</p>
                            </td>
                            <td class="py-3 pr-4"><Badge :color="statusColors[client.status]">{{ client.status }}</Badge></td>
                            <td class="py-3 pr-4 text-slate-600 dark:text-slate-300">{{ client.deals_count }}</td>
                            <td class="py-3 pr-4 text-slate-500">{{ client.owner?.name ?? '—' }}</td>
                            <td class="py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <Link :href="route('crm.clients.edit', client.id)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-brand-600 dark:hover:bg-slate-700"><Icon name="pencil" class="h-4 w-4" /></Link>
                                    <button @click="destroy(client)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-rose-600 dark:hover:bg-slate-700"><Icon name="trash" class="h-4 w-4" /></button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!clients.data.length"><td colspan="5" class="py-12 text-center text-slate-400">No clients found.</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-end"><Pagination :links="clients.links" /></div>
        </Card>
    </AppLayout>
</template>
