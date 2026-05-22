<script setup>
defineProps({ message: String });
</script>

<template>
    <p v-if="message" class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ message }}</p>
</template>
