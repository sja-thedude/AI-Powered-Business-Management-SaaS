<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Icon from '@/Components/Icon.vue';
import { isDark, toggleTheme } from '@/theme';

const page = usePage();
const user = computed(() => page.props.auth.user);
const tenant = computed(() => page.props.tenant);
const nav = computed(() => page.props.nav ?? []);
const flash = computed(() => page.props.flash ?? {});

const sidebarOpen = ref(false);
const userMenuOpen = ref(false);

// Highlight the active section by matching the current URL path.
const currentPath = computed(() => new URL(page.url, 'http://x').pathname);
function isActive(routeName) {
    try {
        return currentPath.value.startsWith('/' + routeName.split('.')[0]);
    } catch (e) {
        return false;
    }
}

// Resolve a module's landing route; fall back to '#' if it isn't registered.
function hrefFor(item) {
    if (item.locked) return undefined;
    try {
        return route(item.route);
    } catch (e) {
        return '#';
    }
}

function logout() {
    router.post(route('logout'));
}
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <!-- Sidebar -->
        <aside
            :class="['fixed inset-y-0 left-0 z-40 w-64 transform border-r border-slate-200 bg-white transition-transform dark:border-slate-800 dark:bg-slate-900 lg:translate-x-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full']"
        >
            <div class="flex h-16 items-center gap-2.5 px-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 text-white">
                    <Icon name="sparkles" class="h-5 w-5" />
                </span>
                <div>
                    <p class="text-sm font-bold leading-tight text-slate-900 dark:text-white">NovaBiz AI</p>
                    <p class="text-[11px] text-slate-400">{{ tenant?.name }}</p>
                </div>
            </div>

            <nav class="mt-2 space-y-1 px-3">
                <Link
                    :href="route('dashboard')"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition"
                    :class="currentPath === '/dashboard' ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-300' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'"
                >
                    <Icon name="grid" class="h-5 w-5" /> Dashboard
                </Link>

                <p class="px-3 pb-1 pt-4 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Modules</p>

                <component
                    v-for="item in nav"
                    :key="item.key"
                    :is="item.locked ? 'div' : Link"
                    :href="hrefFor(item)"
                    class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition"
                    :class="[
                        item.locked
                            ? 'cursor-not-allowed text-slate-400 dark:text-slate-600'
                            : (isActive(item.route) ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-300' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'),
                    ]"
                >
                    <Icon :name="item.icon" class="h-5 w-5" />
                    <span class="flex-1">{{ item.name }}</span>
                    <Icon v-if="item.locked" name="lock" class="h-4 w-4" />
                    <span v-else-if="item.status === 'scaffold'" class="rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">soon</span>
                </component>
            </nav>

            <div class="absolute inset-x-0 bottom-0 space-y-1 border-t border-slate-100 p-3 dark:border-slate-800">
                <Link :href="route('billing.index')" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">
                    <Icon name="credit-card" class="h-5 w-5" /> Billing
                </Link>
                <Link :href="route('settings.index')" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">
                    <Icon name="cog" class="h-5 w-5" /> Settings
                </Link>
            </div>
        </aside>

        <!-- Backdrop (mobile) -->
        <div v-if="sidebarOpen" class="fixed inset-0 z-30 bg-slate-900/40 lg:hidden" @click="sidebarOpen = false" />

        <!-- Main -->
        <div class="lg:pl-64">
            <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-slate-200 bg-white/80 px-4 backdrop-blur dark:border-slate-800 dark:bg-slate-900/80 sm:px-6">
                <button class="text-slate-500 lg:hidden" @click="sidebarOpen = !sidebarOpen">☰</button>

                <div class="hidden flex-1 sm:block">
                    <span v-if="tenant?.on_trial" class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                        <Icon name="sparkles" class="h-3.5 w-3.5" /> Trial · {{ tenant.plan }} plan
                    </span>
                </div>

                <div class="ml-auto flex items-center gap-2">
                    <button class="rounded-xl p-2 text-slate-500 transition hover:bg-slate-100 dark:hover:bg-slate-800" @click="toggleTheme" :title="isDark ? 'Light mode' : 'Dark mode'">
                        <Icon :name="isDark ? 'sun' : 'moon'" class="h-5 w-5" />
                    </button>
                    <button class="rounded-xl p-2 text-slate-500 transition hover:bg-slate-100 dark:hover:bg-slate-800">
                        <Icon name="bell" class="h-5 w-5" />
                    </button>

                    <div class="relative">
                        <button class="flex items-center gap-2 rounded-xl p-1 pr-2 transition hover:bg-slate-100 dark:hover:bg-slate-800" @click="userMenuOpen = !userMenuOpen">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600 text-xs font-semibold text-white">{{ user?.initials }}</span>
                            <span class="hidden text-sm font-medium text-slate-700 dark:text-slate-200 sm:block">{{ user?.name }}</span>
                        </button>
                        <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white py-1 shadow-lg dark:border-slate-800 dark:bg-slate-900" @click="userMenuOpen = false">
                            <div class="border-b border-slate-100 px-4 py-2 dark:border-slate-800">
                                <p class="text-sm font-medium text-slate-900 dark:text-white">{{ user?.name }}</p>
                                <p class="truncate text-xs text-slate-400">{{ user?.email }}</p>
                            </div>
                            <Link :href="route('settings.index')" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800">Settings</Link>
                            <button class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-rose-600 hover:bg-slate-50 dark:hover:bg-slate-800" @click="logout">
                                <Icon name="logout" class="h-4 w-4" /> Sign out
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash toasts -->
            <div v-if="flash.success || flash.error" class="px-4 pt-4 sm:px-6">
                <div v-if="flash.success" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">{{ flash.success }}</div>
                <div v-if="flash.error" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">{{ flash.error }}</div>
            </div>

            <main class="p-4 sm:p-6">
                <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 dark:text-white"><slot name="title">Dashboard</slot></h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400"><slot name="subtitle" /></p>
                    </div>
                    <div><slot name="actions" /></div>
                </div>
                <slot />
            </main>
        </div>
    </div>
</template>
