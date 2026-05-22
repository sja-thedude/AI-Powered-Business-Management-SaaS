<script setup>
defineProps({
    title: { type: String, default: null },
    subtitle: { type: String, default: null },
});
</script>

<template>
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div v-if="title || $slots.actions" class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
            <div>
                <h3 v-if="title" class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ title }}</h3>
                <p v-if="subtitle" class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ subtitle }}</p>
            </div>
            <div v-if="$slots.actions"><slot name="actions" /></div>
        </div>
        <div class="p-5">
            <slot />
        </div>
    </div>
</template>
