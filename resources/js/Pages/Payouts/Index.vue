<script setup>
import { Head, useForm, usePage, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    artists: Array,
    payouts: Object,
    stats: Object,
    transactions: Object,
})

const page = usePage()

const isLabel = computed(() => {
    const roles = page.props.auth?.user?.roles ?? []
    return roles.includes('label')
})

const activeFilter = ref('all')
const filters = [
    { key: 'all', label: 'Все' },
    { key: 'pending', label: 'В ожидании' },
    { key: 'completed', label: 'Завершено' },
]

const filteredPayouts = computed(() => {
    if (!props.payouts?.data) return []
    if (activeFilter.value === 'all') return props.payouts.data
    return props.payouts.data.filter(p => p.status === activeFilter.value)
})

const filteredTransactions = computed(() => {
    if (!props.transactions?.data) return []
    if (activeFilter.value === 'all') return props.transactions.data
    return props.transactions.data.filter(tx => tx.status === activeFilter.value)
})

const form = useForm({
    artist_id: null,
    method: 'bank',
    details: '',
})

function createPayout(artist) {
    form.artist_id = artist.id
    form.post(route('payouts.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    })
}

function confirmPaid(payout) {
    if (!confirm('Подтвердить выплату ' + Number(payout.amount).toLocaleString('ru-RU') + ' ₽?')) return
    router.patch(route('payouts.pay', payout.id), {}, { preserveScroll: true })
}

const fmt = (n) => Number(n).toLocaleString('ru-RU') + ' ₽'
</script>

<template>
    <Head title="Выплаты" />
    <AuthenticatedLayout>
        <div class="min-h-screen p-8" style="background-color: #0B0E14; color: #F8FAFC;">

            <!-- ЛЕЙБЛ -->
            <template v-if="isLabel">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                    <h1 class="text-3xl md:text-4xl font-bold text-white">Выплаты артистам</h1>
<!--                    <button-->
<!--                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium text-white transition shadow-lg shadow-violet-900/20 hover:opacity-90"-->
<!--                        style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%)"-->
<!--                    >-->
<!--                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">-->
<!--                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />-->
<!--                        </svg>-->
<!--                        Инициировать выплату-->
<!--                    </button>-->
                </div>

                <!-- Карточки доступных средств -->
                <div class="mb-10 space-y-3">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wider" style="color: #64748B;">
                        Доступно для выплаты
                    </h2>
                    <div v-if="!artists || artists.length === 0"
                        class="rounded-xl p-6 text-sm"
                        style="background-color: #1A1F2B; border: 1px solid #1e2330; color: #64748B;">
                        Нет начислений в статусе «Ожидает».
                    </div>
                    <div v-else class="space-y-3">
                        <div
                            v-for="artist in artists"
                            :key="artist.id"
                            class="flex items-center justify-between rounded-xl px-6 py-4"
                            style="background-color: #1A1F2B; border: 1px solid #1e2330;">
                            <div>
                                <p class="text-lg font-semibold text-white">{{ artist.artist_name }}</p>
                                <p class="text-xs" style="color: #64748B;">{{ artist.pending_count }} начислений</p>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-white">{{ fmt(artist.pending_amount) }}</p>
                                    <p class="text-[11px]" style="color: #64748B;">доступно</p>
                                </div>
                                <button
                                    @click="createPayout(artist)"
                                    :disabled="form.processing && form.artist_id === artist.id"
                                    class="rounded-lg px-4 py-2 text-sm font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50"
                                    style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                                    Сформировать выплату
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Фильтры -->
                <div class="flex flex-wrap gap-2 mb-6">
                    <button
                        v-for="f in filters"
                        :key="f.key"
                        @click="activeFilter = f.key"
                        class="px-5 py-2 rounded-full text-sm font-medium border transition"
                        :class="activeFilter === f.key ? 'text-white border-transparent' : 'bg-[#1A1F2B] text-slate-400 border-slate-700 hover:text-white hover:border-slate-500'"
                        :style="activeFilter === f.key ? { background: 'linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%)' } : {}"
                    >
                        {{ f.label }}
                    </button>
                </div>

                <!-- История выплат -->
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider" style="color: #64748B;">История выплат</h2>
                <div class="rounded-xl overflow-hidden border border-white/5" style="background-color: #1A1F2B;">
                    <table class="w-full text-left text-sm">
                        <thead style="background-color: #0F1117;">
                            <tr class="text-slate-400">
                                <th class="px-6 py-3 font-medium whitespace-nowrap">Дата</th>
                                <th class="px-6 py-3 font-medium whitespace-nowrap">Артист</th>
                                <th class="px-6 py-3 font-medium whitespace-nowrap text-right">Сумма</th>
                                <th class="px-6 py-3 font-medium whitespace-nowrap text-right">Статус</th>
                                <th class="px-6 py-3 font-medium whitespace-nowrap"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in filteredPayouts" :key="p.id"
                                class="border-t border-white/5 transition-colors hover:bg-white/[0.03]">
                                <td class="px-6 py-4 text-white whitespace-nowrap">
                                    {{ new Date(p.created_at).toLocaleDateString('ru-RU') }}
                                </td>
                                <td class="px-6 py-4 text-white">{{ p.artist?.user?.name ?? '-' }}</td>
                                <td class="px-6 py-4 font-semibold text-white whitespace-nowrap text-right">{{ fmt(p.amount) }}</td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <span v-if="p.status === 'pending'"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400">
                                        В ожидании
                                    </span>
                                    <span v-else
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400">
                                        Выплачено
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button v-if="p.status === 'pending'" @click="confirmPaid(p)"
                                        class="rounded-lg px-3 py-1.5 text-xs font-medium text-white transition-opacity hover:opacity-90"
                                        style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                                        Подтвердить перевод
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="!payouts.data.length" class="px-6 py-8 text-center text-sm" style="color: #64748B;">
                        Пока нет выплат.
                    </div>
                </div>
            </template>

            <!-- АРТИСТ -->
            <template v-else>
                <h1 class="mb-8 text-3xl font-bold">Финансы</h1>

                <!-- Статистика -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6 mb-8">
                    <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/15 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="text-sm text-slate-400 mb-1">Текущий баланс</div>
                        <div class="text-2xl font-bold text-white">{{ fmt(stats?.balance ?? 0) }}</div>
                    </div>
                    <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5">
                        <div class="w-12 h-12 rounded-xl bg-blue-500/15 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                        </div>
                        <div class="text-sm text-slate-400 mb-1">Общий доход</div>
                        <div class="text-2xl font-bold text-white">{{ fmt(stats?.total_earned ?? 0) }}</div>
                    </div>
                    <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5">
                        <div class="w-12 h-12 rounded-xl bg-orange-500/15 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                            </svg>
                        </div>
                        <div class="text-sm text-slate-400 mb-1">Выплачено</div>
                        <div class="text-2xl font-bold text-white">{{ fmt(stats?.total_paid ?? 0) }}</div>
                    </div>
                </div>

                <!-- История операций -->
                <h2 class="mb-4 text-xl font-semibold">История начислений и выплат</h2>
                <div class="rounded-xl overflow-hidden border border-white/5" style="background-color: #1A1F2B;">
                    <table class="w-full text-left text-sm">
                        <thead style="background-color: #0F1117;">
                            <tr class="text-slate-400">
                                <th class="px-6 py-3 font-medium">Дата</th>
                                <th class="px-6 py-3 font-medium">Описание</th>
                                <th class="px-6 py-3 font-medium text-right">Сумма</th>
                                <th class="px-6 py-3 font-medium text-right">Статус</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="tx in filteredTransactions" :key="tx.id"
                                class="border-t border-white/5 transition-colors hover:bg-white/[0.03]">
                                <td class="px-6 py-4 text-white whitespace-nowrap">
                                    {{ new Date(tx.created_at).toLocaleDateString('ru-RU') }}
                                </td>
                                <td class="px-6 py-4 text-white">{{ tx.earning?.song?.title ?? 'Начисление роялти' }}</td>
                                <td class="px-6 py-4 font-semibold whitespace-nowrap text-right"
                                    :class="tx.amount > 0 ? 'text-green-400' : 'text-red-400'">
                                    {{ (tx.amount > 0 ? '+' : '') + Number(tx.amount).toLocaleString('ru-RU') }} ₽
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span v-if="tx.status === 'pending'"
                                        class="rounded-full bg-yellow-500/10 px-2.5 py-1 text-xs font-medium text-yellow-500">Ожидает</span>
                                    <span v-else-if="tx.status === 'on_hold'"
                                        class="rounded-full bg-blue-500/10 px-2.5 py-1 text-xs font-medium text-blue-500">В выплате</span>
                                    <span v-else
                                        class="rounded-full bg-green-500/10 px-2.5 py-1 text-xs font-medium text-green-500">Выплачено</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="!transactions?.data?.length" class="px-6 py-8 text-center text-sm" style="color: #64748B;">
                        Пока нет операций.
                    </div>
                </div>
            </template>
        </div>
    </AuthenticatedLayout>
</template>
