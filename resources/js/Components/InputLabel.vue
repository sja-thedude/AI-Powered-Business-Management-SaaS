<script setup>
defineProps({ value: String });
</script>

<template>
    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
        <span v-if="value">{{ value }}</span>
        <slot v-else />
    </label>
</template>
