<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()

const user = computed(() => page.props.auth?.user ?? {
    name: 'Мария Светлова',
    role: 'Артист'
})

const isLabel = computed(() => {
    const role = page.props.auth?.user?.role
    if (!role) return false
    return (typeof role === 'string' ? role : role?.value) === 'label'
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
    return page.url === href || page.url.startsWith(href + '/')
}
</script>

<template>
    <div class="flex h-screen" style="background-color: #0B0E14; font-family: 'Inter', sans-serif; color: #F8FAFC;">

        <!-- SIDEBAR -->
        <aside
            class="flex h-screen w-[260px] flex-col border-r"
            style="background-color: #0F1117; border-color: #1e2330;"
        >
            <!-- Logo -->
            <div class="flex items-center gap-3 px-6 pt-6 pb-8">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl text-white"
                    style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-[18px] font-bold leading-tight tracking-tight">SoundERP</h1>
                    <p class="text-[11px] leading-tight" style="color: #64748B;">Music Label CRM</p>
                </div>
            </div>

            <!-- Menu -->
            <nav class="flex-1 space-y-1 px-3">
                <Link
                    v-for="item in menuItems"
                    :key="item.name"
                    :href="item.href"
                    class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all"
                    :class="isCurrent(item.href) ? 'text-white' : 'hover:text-white'"
                    :style="isCurrent(item.href) ? { backgroundColor: '#1A1F2B' } : { color: '#94A3B8' }"
                >
                    <!-- Dashboard icon -->
                    <template v-if="item.icon === 'dashboard'">
                        <svg class="h-5 w-5 flex-shrink-0" :style="isCurrent(item.href) ? { color: '#7C3AED' } : { color: '#64748B' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                    </template>

                    <!-- Artists icon -->
                    <template v-if="item.icon === 'artists'">
                        <svg class="h-5 w-5 flex-shrink-0" :style="isCurrent(item.href) ? { color: '#7C3AED' } : { color: '#64748B' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </template>

                    <!-- Tracks icon -->
                    <template v-if="item.icon === 'tracks'">
                        <svg class="h-5 w-5 flex-shrink-0" :style="isCurrent(item.href) ? { color: '#7C3AED' } : { color: '#64748B' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z" />
                        </svg>
                    </template>

                    <!-- Finances / Payouts icon -->
                    <template v-if="item.icon === 'finances'">
                        <svg
                            class="h-5 w-5 flex-shrink-0"
                            :style="isCurrent(item.href) ? { color: '#7C3AED' } : { color: '#64748B' }"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M12 2v20" />
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                    </template>

                    <!-- Reports icon -->
                    <template v-if="item.icon === 'reports'">
                        <svg class="h-5 w-5 flex-shrink-0" :style="isCurrent(item.href) ? { color: '#7C3AED' } : { color: '#64748B' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </template>

                    <span class="flex-1">{{ item.name }}</span>

                    <div
                        v-if="isCurrent(item.href)"
                        class="h-1.5 w-1.5 rounded-full"
                        style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
                    />
                </Link>
            </nav>

            <!-- User card bottom -->
            <div class="mt-auto border-t p-4" style="border-color: #1e2330;">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold text-white"
                        style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
                    >
                        {{ user.name.charAt(0) }}
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="truncate text-sm font-medium text-white">{{ user.name }}</p>
                        <p class="truncate text-xs" style="color: #64748B;">{{ user.role }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN -->
        <main class="flex-1 overflow-y-auto">
            <!-- Баннер приглашения в лейбл (только для артиста) -->
            <div
                v-if="$page.props.pendingInvitation"
                class="mx-6 mt-6 flex items-center justify-between rounded-xl px-6 py-4"
                style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
            >
                <div class="text-sm font-medium text-white">
                    Вас пригласил лейбл
                    <strong>{{ $page.props.pendingInvitation.label?.name ?? '—' }}</strong>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('invitations.show', $page.props.pendingInvitation.token)"
                        class="rounded-lg px-3 py-1.5 text-xs font-medium text-white transition-opacity hover:opacity-90"
                        style="background-color: rgba(255,255,255,0.2);"
                    >
                        Подробнее
                    </Link>
                    <Link
                        :href="route('artists.invitations.accept', $page.props.pendingInvitation.token)"
                        method="post"
                        as="button"
                        class="rounded-lg bg-white px-3 py-1.5 text-xs font-bold transition-opacity hover:opacity-90"
                        style="color: #7C3AED;"
                    >
                        Принять
                    </Link>
                </div>
            </div>

            <slot />
        </main>
    </div>
</template>