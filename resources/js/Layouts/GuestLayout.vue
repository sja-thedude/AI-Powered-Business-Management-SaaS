<script setup>
import Icon from '@/Components/Icon.vue';
</script>

<template>
    <div class="flex min-h-screen">
        <!-- Brand panel -->
        <div class="relative hidden w-1/2 flex-col justify-between bg-brand-600 p-12 text-white lg:flex">
            <div class="flex items-center gap-2.5">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15">
                    <Icon name="sparkles" class="h-6 w-6" />
                </span>
                <span class="text-lg font-bold">NovaBiz AI</span>
            </div>
            <div>
                <h2 class="max-w-md text-3xl font-bold leading-tight">The AI-powered operating system for your entire business.</h2>
                <p class="mt-4 max-w-md text-brand-100">CRM, HR, payroll, inventory, invoicing, projects and a client portal — unified, multi-tenant, and automated.</p>
            </div>
            <p class="text-sm text-brand-200">© {{ new Date().getFullYear() }} NovaBiz AI. All rights reserved.</p>
        </div>

        <!-- Form panel -->
        <div class="flex w-full flex-col items-center justify-center bg-slate-50 p-6 dark:bg-slate-950 lg:w-1/2">
            <div class="w-full max-w-md">
                <div class="mb-8 flex items-center gap-2.5 lg:hidden">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 text-white"><Icon name="sparkles" class="h-5 w-5" /></span>
                    <span class="text-lg font-bold text-slate-900 dark:text-white">NovaBiz AI</span>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
