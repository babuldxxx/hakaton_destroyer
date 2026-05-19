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
    return page.url === href || page.url.startsWith(href + '/')
}
</script>

<template>
    <div class="flex h-screen" style="background-color: #0B0E14; font-family: 'Inter', sans-serif; color: #F8FAFC;">
        <!-- SIDEBAR без изменений -->
        <aside class="flex h-screen w-[260px] flex-col border-r" style="background-color: #0F1117; border-color: #1e2330;">
            <!-- Логотип, меню, иконки — все как в твоей версии -->
            <!-- ... -->
            <nav class="flex-1 space-y-1 px-3">
                <Link v-for="item in menuItems" :key="item.name" :href="item.href"
                    class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all"
                    :class="isCurrent(item.href) ? 'text-white' : 'hover:text-white'"
                    :style="isCurrent(item.href) ? { backgroundColor: '#1A1F2B' } : { color: '#94A3B8' }">
                    <!-- иконки -->
                    <span class="flex-1">{{ item.name }}</span>
                    <div v-if="isCurrent(item.href)" class="h-1.5 w-1.5 rounded-full" style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);" />
                </Link>
            </nav>
            <!-- Профиль внизу -->
        </aside>

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