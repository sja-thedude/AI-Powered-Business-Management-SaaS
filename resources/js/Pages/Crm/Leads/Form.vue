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

const props = defineProps({ lead: { type: Object, default: null } });
const editing = !!props.lead;

const form = useForm({
    name: props.lead?.name ?? '',
    company: props.lead?.company ?? '',
    email: props.lead?.email ?? '',
    phone: props.lead?.phone ?? '',
    source: props.lead?.source ?? 'manual',
    status: props.lead?.status ?? 'new',
    estimated_value: props.lead?.estimated_value ?? 0,
    notes: props.lead?.notes ?? '',
});

function submit() {
    editing
        ? form.put(route('crm.leads.update', props.lead.id))
        : form.post(route('crm.leads.store'));
}
</script>

<template>
    <Head :title="editing ? 'Edit lead' : 'New lead'" />
    <AppLayout>
        <template #title>{{ editing ? 'Edit lead' : 'New lead' }}</template>

        <Card class="max-w-2xl">
            <form class="space-y-5" @submit.prevent="submit">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel value="Name" />
                        <TextInput v-model="form.name" autofocus />
                        <InputError :message="form.errors.name" />
                    </div>
                    <div>
                        <InputLabel value="Company" />
                        <TextInput v-model="form.company" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel value="Email" />
                        <TextInput v-model="form.email" type="email" />
                        <InputError :message="form.errors.email" />
                    </div>
                    <div>
                        <InputLabel value="Phone" />
                        <TextInput v-model="form.phone" />
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <InputLabel value="Source" />
                        <SelectInput v-model="form.source" :options="['web','referral','ads','manual','import']" />
                    </div>
                    <div>
                        <InputLabel value="Status" />
                        <SelectInput v-model="form.status" :options="['new','contacted','qualified','unqualified','converted']" />
                    </div>
                    <div>
                        <InputLabel value="Est. value" />
                        <TextInput v-model="form.estimated_value" type="number" />
                    </div>
                </div>
                <div>
                    <InputLabel value="Notes" />
                    <textarea v-model="form.notes" rows="4" class="block w-full rounded-xl border-slate-300 bg-white px-3.5 py-2.5 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <Link :href="route('crm.leads.index')"><SecondaryButton>Cancel</SecondaryButton></Link>
                    <PrimaryButton :disabled="form.processing">{{ editing ? 'Save changes' : 'Create lead' }}</PrimaryButton>
                </div>
            </form>
        </Card>
    </AppLayout>
</template>
