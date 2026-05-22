<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({ type: [String, Number], default: '' });
defineProps({ type: { type: String, default: 'text' }, autofocus: Boolean });

const input = ref(null);
onMounted(() => { if (input.value?.hasAttribute('autofocus')) input.value.focus(); });
defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        v-model="model"
        :type="type"
        class="block w-full rounded-xl border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"
    />
</template>
