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
    const dashboardIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>'
    const artistsIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>'
    const tracksIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z" /></svg>'
    const financesIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20" /><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" /></svg>'
    const reportsIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>'

    if (isLabel.value) {
        return [
            { name: 'Дашборд', href: '/dashboard', icon: dashboardIcon },
            { name: 'Артисты', href: '/artists', icon: artistsIcon },
            { name: 'Треки', href: '/tracks', icon: tracksIcon },
            { name: 'Выплаты', href: '/payouts', icon: financesIcon },
            { name: 'Отчёты', href: '/reports', icon: reportsIcon },
        ]
    }
    return [
        { name: 'Дашборд', href: '/dashboard', icon: dashboardIcon },
        { name: 'Мои треки', href: '/tracks', icon: tracksIcon },
        { name: 'Финансы', href: '/payouts', icon: financesIcon },
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
                        <p class="text-[11px] text-slate-500 mt-1 tracking-wide">Music Label ERP</p>
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
                        :class="isCurrent(item.href) ? 'text-violet-400' : 'text-slate-400 group-hover:text-slate-300'"
                        v-html="item.icon"
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
