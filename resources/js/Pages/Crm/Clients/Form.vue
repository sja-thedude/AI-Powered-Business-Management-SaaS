<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({ client: { type: Object, default: null } });
const editing = !!props.client;

const form = useForm({
    name: props.client?.name ?? '',
    company: props.client?.company ?? '',
    email: props.client?.email ?? '',
    phone: props.client?.phone ?? '',
    website: props.client?.website ?? '',
    status: props.client?.status ?? 'active',
    address: props.client?.address ?? '',
});

function submit() {
    editing
        ? form.put(route('crm.clients.update', props.client.id))
        : form.post(route('crm.clients.store'));
}
</script>

<template>
    <Head :title="editing ? 'Edit client' : 'New client'" />
    <AppLayout>
        <template #title>{{ editing ? 'Edit client' : 'New client' }}</template>
        <Card class="max-w-2xl">
            <form class="space-y-5" @submit.prevent="submit">
                <div class="grid grid-cols-2 gap-4">
                    <div><InputLabel value="Name" /><TextInput v-model="form.name" autofocus /><InputError :message="form.errors.name" /></div>
                    <div><InputLabel value="Company" /><TextInput v-model="form.company" /></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><InputLabel value="Email" /><TextInput v-model="form.email" type="email" /><InputError :message="form.errors.email" /></div>
                    <div><InputLabel value="Phone" /><TextInput v-model="form.phone" /></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><InputLabel value="Website" /><TextInput v-model="form.website" /><InputError :message="form.errors.website" /></div>
                    <div><InputLabel value="Status" /><SelectInput v-model="form.status" :options="['active','prospect','inactive']" /></div>
                </div>
                <div><InputLabel value="Address" /><textarea v-model="form.address" rows="3" class="block w-full rounded-xl border-slate-300 bg-white px-3.5 py-2.5 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"></textarea></div>
                <div class="flex justify-end gap-3">
                    <Link :href="route('crm.clients.index')"><SecondaryButton>Cancel</SecondaryButton></Link>
                    <PrimaryButton :disabled="form.processing">{{ editing ? 'Save changes' : 'Create client' }}</PrimaryButton>
                </div>
            </form>
        </Card>
    </AppLayout>
</template>
