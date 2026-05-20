<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()

const user = computed(() => {
    const authUser = page.props.auth?.user
    if (authUser) {
        return {
            name: authUser.name,
            role: authUser.roles?.[0] ?? 'artist'
        }
    }
    return { name: 'Мария Светлова', role: 'Артист' }
})

const isLabel = computed(() => {
    const roles = page.props.auth?.user?.roles ?? []
    return roles.includes('label')
})

const menuItems = computed(() => {
    if (isLabel.value) {
        return [
            { name: 'Дашборд', href: '/dashboard', icon: 'dashboard' },
            { name: 'Артисты', href: '/artists', icon: 'artists' },
            { name: 'Треки', href: '/tracks', icon: 'tracks' },
            { name: 'Выплаты', href: '/payouts', icon: 'finances' },
            { name: 'Отчёты', href: '/reports', icon: 'reports' },
        ]
    }
    return [
        { name: 'Дашборд', href: '/dashboard', icon: 'dashboard' },
        { name: 'Мои треки', href: '/tracks', icon: 'tracks' },
        { name: 'Финансы', href: '/payouts', icon: 'finances' },
    ]
})

function isCurrent(href) {
    if (href === '#') return false
    return page.url === href || page.url.startsWith(href + '/')
}
</script>

<template>
    <div class="flex min-h-screen bg-[#0B0E14]">

        <!-- Sidebar -->
        <aside class="w-64 flex flex-col bg-[#0B0E14] border-r border-white/10 sticky top-0 h-screen">

            <!-- Logo -->
            <div class="p-6 pb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/20"
                         style="background: linear-gradient(135deg, #8B5CF6 0%, #3B82F6 100%);">
                        <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
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
                    <div
                        v-if="isCurrent(item.href)"
                        class="absolute left-0 top-2.5 bottom-2.5 w-[3px] rounded-r-full bg-gradient-to-b from-violet-400 to-blue-500"
                    />
                    <span class="relative z-10">{{ item.name }}</span>
                </Link>
            </nav>

            <!-- User block bottom -->
            <div class="p-4 mx-4 mb-4">
                <div class="flex items-center gap-3 pt-4 border-t border-white/10">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-500 to-blue-500 flex items-center justify-center text-white font-bold text-sm shadow-md">
                        {{ user.name.charAt(0) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ user.name }}</p>
                        <p class="text-xs text-slate-500">{{ isLabel ? 'Лейбл' : 'Артист' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 overflow-y-auto">
            <!-- Баннер приглашения -->
            <div v-if="$page.props.pendingInvitation"
                 class="mx-6 mt-6 flex items-center justify-between rounded-xl px-6 py-4"
                 style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                <div class="text-sm font-medium text-white">
                    Вас пригласил лейбл
                    <strong>{{ $page.props.pendingInvitation.label?.name ?? '—' }}</strong>
                </div>
                <div class="flex items-center gap-3">
                    <Link :href="route('artists.invitations')"
                          class="rounded-lg px-3 py-1.5 text-xs font-medium text-white transition-opacity hover:opacity-90"
                          style="background-color: rgba(255,255,255,0.2);">
                        Подробнее
                    </Link>
                    <Link :href="route('artists.invitations.accept', $page.props.pendingInvitation.id)"
                          method="post" as="button"
                          class="rounded-lg bg-white px-3 py-1.5 text-xs font-bold transition-opacity hover:opacity-90"
                          style="color: #7C3AED;">
                        Принять
                    </Link>
                </div>
            </div>
            <slot />
        </main>
    </div>
</template>
