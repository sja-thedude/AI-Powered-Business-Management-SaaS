<script setup>
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import Icon from '@/Components/Icon.vue';
import { date } from '@/format';

const props = defineProps({ plans: Object, current: Object });

function choose(key) {
    if (props.current.subscribed) {
        if (confirm(`Switch to the ${props.plans[key].name} plan?`)) router.post(route('billing.swap'), { plan: key });
    } else {
        router.post(route('billing.subscribe'), { plan: key });
    }
}
function cancel() { if (confirm('Cancel your subscription?')) router.post(route('billing.cancel')); }
function resume() { router.post(route('billing.resume')); }
function portal() { router.get(route('billing.portal')); }
</script>

<template>
    <Head title="Billing" />
    <AppLayout>
        <template #title>Billing & Plans</template>
        <template #subtitle>Manage your workspace subscription.</template>
        <template #actions>
            <button v-if="current.subscribed" @click="portal" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200">Manage payment methods</button>
        </template>

        <Card class="mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Current plan</p>
                    <p class="mt-1 flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-white">
                        {{ plans[current.plan]?.name ?? current.plan }}
                        <Badge v-if="current.on_trial" color="amber">Trial</Badge>
                        <Badge v-else-if="current.cancelled" color="rose">Cancelled</Badge>
                        <Badge v-else-if="current.subscribed" color="emerald">Active</Badge>
                    </p>
                    <p v-if="current.on_trial && current.trial_ends" class="mt-1 text-xs text-slate-400">Trial ends {{ date(current.trial_ends) }}</p>
                    <p v-else-if="current.on_grace && current.ends_at" class="mt-1 text-xs text-slate-400">Access until {{ date(current.ends_at) }}</p>
                </div>
                <div class="flex gap-2">
                    <button v-if="current.on_grace" @click="resume" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Resume</button>
                    <button v-else-if="current.subscribed" @click="cancel" class="rounded-xl border border-rose-300 px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50 dark:border-rose-500/40 dark:hover:bg-rose-500/10">Cancel subscription</button>
                </div>
            </div>
        </Card>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <Card v-for="(plan, key) in plans" :key="key" :class="key === current.plan ? 'ring-2 ring-brand-500' : ''">
                <div class="flex items-baseline justify-between">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ plan.name }}</h3>
                    <p><span class="text-2xl font-extrabold text-slate-900 dark:text-white">${{ plan.price }}</span><span class="text-sm text-slate-400">/mo</span></p>
                </div>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ plan.description }}</p>
                <ul class="mt-4 space-y-2">
                    <li v-for="f in plan.features" :key="f" class="flex items-start gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <Icon name="check" class="mt-0.5 h-4 w-4 text-emerald-500" /> {{ f }}
                    </li>
                </ul>
                <button
                    @click="choose(key)"
                    :disabled="key === current.plan && current.subscribed"
                    class="mt-5 w-full rounded-xl py-2.5 text-sm font-semibold transition disabled:opacity-50"
                    :class="key === current.plan ? 'bg-slate-100 text-slate-500 dark:bg-slate-800' : 'bg-brand-600 text-white hover:bg-brand-700'"
                >
                    {{ key === current.plan && current.subscribed ? 'Current plan' : (current.subscribed ? 'Switch plan' : 'Subscribe') }}
                </button>
            </Card>
        </div>
    </AppLayout>
</template>
