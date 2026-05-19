<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()

const user = computed(() => page.props.auth?.user ?? {
    name: 'Мария Светлова',
    role: 'Артист'
})

const menuItems = [
    {
        name: 'Дашборд',
        href: '/artist/dashboard',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>'
    },
    {
        name: 'Мои треки',
        href: '/tracks',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z" /></svg>'
    },
    {
        name: 'Финансы',
        href: '/finances',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20" /><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" /></svg>'
    },
]

function isCurrent(href) {
    if (href === '#') return false
    return page.url === href || page.url.startsWith(href + '/')
}
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-[#0B0E14]">

        <!-- SIDEBAR -->
        <aside
            class="flex h-full w-[260px] shrink-0 flex-col border-r border-white/10 bg-[#0B0E14] overflow-y-auto"
        >
            <!-- Logo -->
            <div class="p-6 pb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/20"
                style="background: linear-gradient(135deg, #8B5CF6 0%, #3B82F6 100%);">
                
                <!-- Audio-wave / Equalizer icon -->
                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 3a1 1 0 0 1 1 1v16a1 1 0 1 1-2 0V4a1 1 0 0 1 1-1zM7 7a1 1 0 0 1 1 1v8a1 1 0 1 1-2 0V8a1 1 0 0 1 1-1zM17 6a1 1 0 0 1 1 1v10a1 1 0 1 1-2 0V7a1 1 0 0 1 1-1zM2 11a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0v-2a1 1 0 0 1 1-1zM22 10a1 1 0 0 1 1 1v4a1 1 0 1 1-2 0v-4a1 1 0 0 1 1-1z"/>
                </svg>
                </div>
                <div>
                <h1 class="text-xl font-bold leading-none bg-clip-text text-transparent bg-gradient-to-r from-violet-400 to-blue-400">
                    SoundERP
                </h1>
                <p class="text-[11px] text-slate-500 mt-1 tracking-wide">Music Label CRM</p>
                </div>
            </div>
            </div>

            <!-- Divider -->
            <div class="mx-6 h-px bg-white/10 mb-4" />

            <!-- Navigation -->
            <nav class="flex-1 px-4 space-y-1">
                <Link
                    v-for="item in menuItems"
                    :key="item.name"
                    :href="item.href"
                    class="group flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-medium transition relative overflow-hidden"
                    :class="isCurrent(item.href)
                        ? 'bg-[#151b2e] text-white shadow-[inset_0_0_0_1px_rgba(124,58,237,0.08)]'
                        : 'text-slate-400 hover:text-white hover:bg-white/5'"
                >
                    <!-- Active left accent -->
                    <div
                        v-if="isCurrent(item.href)"
                        class="absolute left-0 top-2.5 bottom-2.5 w-[3px] rounded-r-full bg-gradient-to-b from-violet-400 to-blue-500"
                    />

                    <div
                        class="shrink-0"
                        :class="isCurrent(item.href) ? 'text-violet-400' : 'text-slate-500 group-hover:text-slate-300'"
                        v-html="item.icon"
                    />
                    <span class="relative z-10">{{ item.name }}</span>
                </Link>
            </nav>

            <!-- User card bottom -->
            <div class="p-4 mx-4 mb-4">
                <div class="flex items-center gap-3 pt-4 border-t border-white/10">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold text-white"
                        style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
                    >
                        {{ user.name.charAt(0) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-white">{{ user.name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ user.role }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN -->
        <main class="flex-1 overflow-y-auto">
            <slot />
        </main>
    </div>
</template>