<script setup>
const model = defineModel({ type: [String, Number, null], default: '' });
defineProps({ options: { type: Array, default: () => [] } });
</script>

<template>
    <select
        v-model="model"
        class="block w-full rounded-xl border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm transition focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"
    >
        <slot>
            <option v-for="opt in options" :key="opt.value ?? opt" :value="opt.value ?? opt">
                {{ opt.label ?? opt }}
            </option>
        </slot>
    </select>
</template>
