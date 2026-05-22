<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({ workspace: Object, timezones: Array });

const form = useForm({
    name: props.workspace.name,
    timezone: props.workspace.timezone,
    currency: props.workspace.currency,
});

function save() {
    form.put(route('settings.workspace'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Settings" />
    <AppLayout>
        <template #title>Settings</template>
        <template #subtitle>Workspace configuration & administration.</template>

        <div class="mb-6 flex gap-2">
            <Link :href="route('settings.index')" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white">Workspace</Link>
            <Link :href="route('settings.team')" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">Team</Link>
            <Link :href="route('settings.audit')" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">Audit log</Link>
        </div>

        <Card title="Workspace" class="max-w-2xl">
            <form class="space-y-5" @submit.prevent="save">
                <div>
                    <InputLabel value="Workspace name" />
                    <TextInput v-model="form.name" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel value="Timezone" />
                        <SelectInput v-model="form.timezone" :options="timezones" />
                    </div>
                    <div>
                        <InputLabel value="Currency" />
                        <TextInput v-model="form.currency" />
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <PrimaryButton :disabled="form.processing">Save settings</PrimaryButton>
                    <span class="text-xs text-slate-400">Slug: {{ workspace.slug }} · Plan: {{ workspace.plan }}</span>
                </div>
            </form>
        </Card>
    </AppLayout>
</template>
