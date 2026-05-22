<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Badge from './Badge.vue';
import Icon from './Icon.vue';
import { relative } from '@/format';

const props = defineProps({
    subjectType: { type: String, required: true }, // lead|client|deal
    subjectId: { type: Number, required: true },
    activities: { type: Array, default: () => [] },
});

const typeColors = { note: 'slate', call: 'brand', email: 'violet', meeting: 'amber', task: 'emerald' };

const form = useForm({
    subject_type: props.subjectType,
    subject_id: props.subjectId,
    type: 'note',
    title: '',
    body: '',
});

function submit() {
    form.post(route('crm.activities.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('title', 'body'),
    });
}

function remove(id) {
    router.delete(route('crm.activities.destroy', id), { preserveScroll: true });
}
</script>

<template>
    <div>
        <form class="mb-5 space-y-3 rounded-xl border border-slate-200 p-4 dark:border-slate-800" @submit.prevent="submit">
            <div class="flex gap-2">
                <select v-model="form.type" class="rounded-lg border-slate-300 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                    <option v-for="t in ['note','call','email','meeting','task']" :key="t" :value="t">{{ t }}</option>
                </select>
                <input v-model="form.title" placeholder="Subject (optional)" class="flex-1 rounded-lg border-slate-300 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" />
            </div>
            <textarea v-model="form.body" rows="2" placeholder="Log a note, call summary, email…" class="block w-full rounded-lg border-slate-300 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"></textarea>
            <div class="flex justify-end">
                <button type="submit" :disabled="form.processing" class="rounded-lg bg-brand-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-50">Log activity</button>
            </div>
        </form>

        <ol class="relative space-y-4 border-l border-slate-200 pl-5 dark:border-slate-800">
            <li v-for="a in activities" :key="a.id" class="relative">
                <span class="absolute -left-[27px] flex h-5 w-5 items-center justify-center rounded-full bg-brand-100 text-brand-600 dark:bg-brand-500/20"><Icon name="check" class="h-3 w-3" /></span>
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <Badge :color="typeColors[a.type]">{{ a.type }}</Badge>
                            <span v-if="a.title" class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ a.title }}</span>
                        </div>
                        <p v-if="a.body" class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ a.body }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ a.user?.name }} · {{ relative(a.created_at) }}</p>
                    </div>
                    <button @click="remove(a.id)" class="text-slate-300 hover:text-rose-500"><Icon name="trash" class="h-4 w-4" /></button>
                </div>
            </li>
            <li v-if="!activities.length" class="text-sm text-slate-400">No activity logged yet.</li>
        </ol>
    </div>
</template>
