<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({ canRegister: Boolean });

const form = useForm({ email: '', password: '', remember: false });

function submit() {
    form.post(route('login'), { onFinish: () => form.reset('password') });
}
</script>

<template>
    <GuestLayout>
        <Head title="Sign in" />
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Welcome back</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Sign in to your NovaBiz AI workspace.</p>

        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <div>
                <InputLabel value="Email" />
                <TextInput v-model="form.email" type="email" autofocus />
                <InputError :message="form.errors.email" />
            </div>
            <div>
                <InputLabel value="Password" />
                <TextInput v-model="form.password" type="password" />
                <InputError :message="form.errors.password" />
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                <input v-model="form.remember" type="checkbox" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500" />
                Remember me
            </label>
            <PrimaryButton class="w-full" :disabled="form.processing">Sign in</PrimaryButton>
        </form>

        <p v-if="canRegister" class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
            New to NovaBiz AI?
            <Link :href="route('register')" class="font-semibold text-brand-600 hover:underline">Create a workspace</Link>
        </p>
        <p class="mt-4 rounded-lg bg-slate-100 px-3 py-2 text-center text-xs text-slate-500 dark:bg-slate-800 dark:text-slate-400">
            Demo: owner@novabiz.test / password
        </p>
    </GuestLayout>
</template>
