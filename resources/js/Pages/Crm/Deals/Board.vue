<script setup>
import { reactive, ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Icon from '@/Components/Icon.vue';
import { money } from '@/format';

const props = defineProps({ pipeline: Object, pipelines: Array, clients: Array, summary: Object });
const page = usePage();

// Local, reactive board state so drag-and-drop feels instant (optimistic UI).
const stages = reactive(props.pipeline.stages.map(s => ({ ...s, deals: [...(s.deals || [])] })));
watch(() => props.pipeline, (p) => {
    stages.splice(0, stages.length, ...p.stages.map(s => ({ ...s, deals: [...(s.deals || [])] })));
});

const dragging = ref(null); // { dealId, fromStageId }

function onDragStart(deal, stageId) {
    dragging.value = { dealId: deal.id, fromStageId: stageId };
}

function onDrop(targetStageId) {
    const move = dragging.value;
    dragging.value = null;
    if (!move || move.fromStageId === targetStageId) return;

    const from = stages.find(s => s.id === move.fromStageId);
    const to = stages.find(s => s.id === targetStageId);
    const idx = from.deals.findIndex(d => d.id === move.dealId);
    if (idx === -1) return;

    const [card] = from.deals.splice(idx, 1);
    to.deals.push(card);

    router.patch(route('crm.deals.move', move.dealId), {
        pipeline_stage_id: targetStageId,
        position: to.deals.length - 1,
    }, { preserveScroll: true, preserveState: true });
}

function stageTotal(stage) {
    return stage.deals.reduce((sum, d) => sum + Number(d.value || 0), 0);
}

/* --- New deal modal --- */
const showCreate = ref(false);
const createForm = useForm({
    title: '', value: 0, client_id: '', pipeline_id: props.pipeline.id,
    pipeline_stage_id: props.pipeline.stages[0]?.id, currency: 'USD',
});
function createDeal() {
    createForm.post(route('crm.deals.store'), {
        preserveScroll: true,
        onSuccess: () => { showCreate.value = false; createForm.reset('title', 'value', 'client_id'); router.reload({ only: ['pipeline', 'summary'] }); },
    });
}

function won(deal) { router.post(route('crm.deals.won', deal.id), {}, { preserveScroll: true, onSuccess: () => router.reload({ only: ['pipeline', 'summary'] }) }); }
function lost(deal) {
    const reason = prompt('Reason for losing this deal? (optional)');
    router.post(route('crm.deals.lost', deal.id), { reason }, { preserveScroll: true, onSuccess: () => router.reload({ only: ['pipeline', 'summary'] }) });
}

/* --- Realtime: other users moving cards --- */
let channel = null;
onMounted(() => {
    try {
        const tenantId = page.props.tenant?.id;
        if (window.Echo && tenantId) {
            channel = window.Echo.private(`tenant.${tenantId}.crm`)
                .listen('.App\\Events\\Crm\\DealMoved', () => router.reload({ only: ['pipeline'] }));
        }
    } catch (e) { /* Reverb not running in local dev — safe to ignore */ }
});
onUnmounted(() => {
    try { if (channel) window.Echo.leave(`tenant.${page.props.tenant?.id}.crm`); } catch (e) {}
});

function switchPipeline(e) {
    router.get(route('crm.deals.board'), { pipeline: e.target.value }, { preserveState: false });
}
</script>

<template>
    <Head title="Sales Pipeline" />
    <AppLayout>
        <template #title>Sales Pipeline</template>
        <template #subtitle>{{ summary.open_count }} open deals · {{ money(summary.open_value) }} in play</template>
        <template #actions>
            <div class="flex items-center gap-2">
                <select v-if="pipelines.length > 1" @change="switchPipeline" class="rounded-xl border-slate-300 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                    <option v-for="p in pipelines" :key="p.id" :value="p.id" :selected="p.id === pipeline.id">{{ p.name }}</option>
                </select>
                <button @click="showCreate = true" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
                    <Icon name="plus" class="h-4 w-4" /> New deal
                </button>
            </div>
        </template>

        <div class="flex gap-4 overflow-x-auto pb-4">
            <div
                v-for="stage in stages" :key="stage.id"
                class="flex w-72 flex-shrink-0 flex-col rounded-2xl bg-slate-100/60 dark:bg-slate-900/60"
                @dragover.prevent
                @drop="onDrop(stage.id)"
            >
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full" :style="{ backgroundColor: stage.color }" />
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ stage.name }}</h3>
                        <span class="rounded-full bg-slate-200 px-2 text-xs text-slate-500 dark:bg-slate-800">{{ stage.deals.length }}</span>
                    </div>
                    <span class="text-xs font-medium text-slate-400">{{ money(stageTotal(stage)) }}</span>
                </div>

                <div class="flex-1 space-y-2 px-2 pb-2">
                    <div
                        v-for="deal in stage.deals" :key="deal.id"
                        draggable="true"
                        @dragstart="onDragStart(deal, stage.id)"
                        class="group cursor-grab rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition hover:shadow-md active:cursor-grabbing dark:border-slate-800 dark:bg-slate-900"
                    >
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ deal.title }}</p>
                        <p v-if="deal.client" class="mt-0.5 text-xs text-slate-400">{{ deal.client.name }}</p>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-sm font-bold text-brand-600 dark:text-brand-400">{{ money(deal.value) }}</span>
                            <div class="flex gap-1 opacity-0 transition group-hover:opacity-100">
                                <button @click="won(deal)" title="Won" class="rounded-md p-1 text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-500/10"><Icon name="check" class="h-4 w-4" /></button>
                                <button @click="lost(deal)" title="Lost" class="rounded-md p-1 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10">✕</button>
                            </div>
                        </div>
                    </div>
                    <p v-if="!stage.deals.length" class="px-2 py-6 text-center text-xs text-slate-400">Drop deals here</p>
                </div>
            </div>
        </div>

        <Modal :show="showCreate" title="New deal" @close="showCreate = false">
            <form class="space-y-4" @submit.prevent="createDeal">
                <div><InputLabel value="Title" /><TextInput v-model="createForm.title" autofocus /></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><InputLabel value="Value" /><TextInput v-model="createForm.value" type="number" /></div>
                    <div>
                        <InputLabel value="Stage" />
                        <SelectInput v-model="createForm.pipeline_stage_id" :options="pipeline.stages.map(s => ({ value: s.id, label: s.name }))" />
                    </div>
                </div>
                <div>
                    <InputLabel value="Client" />
                    <SelectInput v-model="createForm.client_id">
                        <option value="">— None —</option>
                        <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </SelectInput>
                </div>
                <div class="flex justify-end"><PrimaryButton :disabled="createForm.processing">Create deal</PrimaryButton></div>
            </form>
        </Modal>
    </AppLayout>
</template>
