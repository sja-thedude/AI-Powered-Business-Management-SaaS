<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import { relative } from '@/format';

defineProps({ members: Array, roles: Array });
</script>

<template>
    <Head title="Team" />
    <AppLayout>
        <template #title>Team</template>
        <template #subtitle>People in your workspace and their roles.</template>

        <div class="mb-6 flex gap-2">
            <Link :href="route('settings.index')" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">Workspace</Link>
            <Link :href="route('settings.team')" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white">Team</Link>
            <Link :href="route('settings.audit')" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">Audit log</Link>
        </div>

        <Card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400 dark:border-slate-800">
                            <th class="py-3 pr-4 font-medium">Member</th>
                            <th class="py-3 pr-4 font-medium">Position</th>
                            <th class="py-3 pr-4 font-medium">Roles</th>
                            <th class="py-3 pr-4 font-medium">Status</th>
                            <th class="py-3 font-medium">Last login</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-for="m in members" :key="m.id">
                            <td class="py-3 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600 text-xs font-semibold text-white">{{ m.name.split(' ').map(p => p[0]).slice(0,2).join('') }}</span>
                                    <div>
                                        <p class="font-medium text-slate-800 dark:text-slate-200">{{ m.name }}</p>
                                        <p class="text-xs text-slate-400">{{ m.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 pr-4 text-slate-500">{{ m.position ?? '—' }}</td>
                            <td class="py-3 pr-4"><Badge v-for="r in m.roles" :key="r.id" color="brand" class="mr-1">{{ r.name }}</Badge></td>
                            <td class="py-3 pr-4"><Badge :color="m.is_active ? 'emerald' : 'slate'">{{ m.is_active ? 'active' : 'inactive' }}</Badge></td>
                            <td class="py-3 text-slate-500">{{ m.last_login_at ? relative(m.last_login_at) : 'never' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
    </AppLayout>
</template>
