<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({ plans: { type: Object, default: () => ({}) } });

const form = useForm({
    company: '', name: '', email: '', password: '', password_confirmation: '',
    plan: 'starter',
});

function submit() {
    form.post(route('register'), { onFinish: () => form.reset('password', 'password_confirmation') });
}
</script>

<template>
    <GuestLayout>
        <Head title="Create your workspace" />
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Start your free trial</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">14 days free. No credit card required.</p>

        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <div>
                <InputLabel value="Company / Workspace name" />
                <TextInput v-model="form.company" autofocus />
                <InputError :message="form.errors.company" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel value="Your name" />
                    <TextInput v-model="form.name" />
                    <InputError :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel value="Work email" />
                    <TextInput v-model="form.email" type="email" />
                    <InputError :message="form.errors.email" />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel value="Password" />
                    <TextInput v-model="form.password" type="password" />
                    <InputError :message="form.errors.password" />
                </div>
                <div>
                    <InputLabel value="Confirm password" />
                    <TextInput v-model="form.password_confirmation" type="password" />
                </div>
            </div>
            <div>
                <InputLabel value="Choose a plan" />
                <div class="grid grid-cols-3 gap-2">
                    <button
                        v-for="(plan, key) in plans" :key="key" type="button"
                        @click="form.plan = key"
                        class="rounded-xl border p-3 text-left text-xs transition"
                        :class="form.plan === key ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10' : 'border-slate-200 hover:border-slate-300 dark:border-slate-700'"
                    >
                        <span class="block font-semibold text-slate-900 dark:text-white">{{ plan.name }}</span>
                        <span class="text-slate-500 dark:text-slate-400">${{ plan.price }}/mo</span>
                    </button>
                </div>
            </div>
            <PrimaryButton class="w-full" :disabled="form.processing">Create workspace</PrimaryButton>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
            Already have an account?
            <Link :href="route('login')" class="font-semibold text-brand-600 hover:underline">Sign in</Link>
        </p>
    </GuestLayout>
</template>
