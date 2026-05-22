<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Icon from '@/Components/Icon.vue';

defineProps({
    plans: { type: Object, default: () => ({}) },
    modules: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="The AI Business Operating System" />
    <div class="min-h-screen bg-white dark:bg-slate-950">
        <header class="mx-auto flex max-w-6xl items-center justify-between px-6 py-5">
            <div class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 text-white"><Icon name="sparkles" class="h-5 w-5" /></span>
                <span class="text-lg font-bold text-slate-900 dark:text-white">NovaBiz AI</span>
            </div>
            <div class="flex items-center gap-3">
                <Link :href="route('login')" class="text-sm font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-300">Sign in</Link>
                <Link :href="route('register')" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Start free</Link>
            </div>
        </header>

        <section class="mx-auto max-w-4xl px-6 py-20 text-center">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-medium text-brand-700 dark:bg-brand-500/10 dark:text-brand-300">
                <Icon name="sparkles" class="h-3.5 w-3.5" /> AI-powered · Multi-tenant · API-first
            </span>
            <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-6xl">
                Run your entire business on one <span class="text-brand-600">AI platform</span>
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg text-slate-600 dark:text-slate-300">
                CRM, HR, payroll, inventory, invoicing, projects and a client portal — unified into a single
                modular ERP, supercharged with AI automation. Think Odoo × Zoho × HubSpot.
            </p>
            <div class="mt-8 flex justify-center gap-3">
                <Link :href="route('register')" class="rounded-xl bg-brand-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Start your free trial</Link>
                <Link :href="route('login')" class="rounded-xl border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200">Live demo</Link>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-6 pb-20">
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                <div v-for="m in modules" :key="m.key" class="rounded-2xl border border-slate-200 p-5 dark:border-slate-800">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-300"><Icon :name="m.icon" class="h-5 w-5" /></span>
                    <h3 class="mt-3 text-sm font-semibold text-slate-900 dark:text-white">{{ m.name }}</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ m.description }}</p>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-6 pb-24">
            <h2 class="text-center text-2xl font-bold text-slate-900 dark:text-white">Simple, scalable pricing</h2>
            <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-3">
                <div v-for="(plan, key) in plans" :key="key" class="rounded-2xl border border-slate-200 p-6 dark:border-slate-800" :class="key === 'growth' ? 'ring-2 ring-brand-500' : ''">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ plan.name }}</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ plan.description }}</p>
                    <p class="mt-4"><span class="text-4xl font-extrabold text-slate-900 dark:text-white">${{ plan.price }}</span><span class="text-slate-500">/mo</span></p>
                    <ul class="mt-6 space-y-2">
                        <li v-for="f in plan.features" :key="f" class="flex items-start gap-2 text-sm text-slate-600 dark:text-slate-300">
                            <Icon name="check" class="mt-0.5 h-4 w-4 text-emerald-500" /> {{ f }}
                        </li>
                    </ul>
                    <Link :href="route('register')" class="mt-6 block rounded-xl bg-brand-600 py-2.5 text-center text-sm font-semibold text-white hover:bg-brand-700">Get started</Link>
                </div>
            </div>
        </section>

        <footer class="border-t border-slate-100 py-8 text-center text-sm text-slate-400 dark:border-slate-800">
            © {{ new Date().getFullYear() }} NovaBiz AI · Built with Laravel 12, Inertia + Vue, PostgreSQL & Reverb
        </footer>
    </div>
</template>
