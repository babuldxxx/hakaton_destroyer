<script setup>
import { usePage, Head } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import StatCard from '@/Components/StatCard.vue'
import LineChart from '@/Components/Charts/LineChart.vue'

const props = defineProps({
    stats: Object,
    topTracks: Array,
    revenueData: Object,
})

const page = usePage()
const user = computed(() => page.props.auth?.user ?? { name: 'Артист' })

const today = new Intl.DateTimeFormat('ru-RU', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
}).format(new Date())

const selectedPeriod = ref('year')
const periods = [
    { key: 'year', label: 'Год' },
]
</script>

<template>
    <Head title="Дашборд артиста" />
    <AuthenticatedLayout>
        <div class="p-6 md:p-10">
            <div class="mx-auto max-w-6xl">
                <!-- Хедер -->
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-[32px] font-bold leading-tight" style="color: #F8FAFC;">
                            Привет, {{ user.name }}
                        </h1>
                        <p class="mt-2 text-base" style="color: #94A3B8;">
                            Сегодня: {{ today }}
                        </p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-xl border-2"
                         style="border-color: #7C3AED;">
                        <span class="flex h-full w-full items-center justify-center text-lg font-bold text-white"
                              style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                            {{ user.name.charAt(0) }}
                        </span>
                    </div>
                </div>

                <!-- KPI Grid -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <StatCard title="Мой баланс"
                              :value="stats.balance"
                              subtitle="Доступно для вывода"
                              iconBg="bg-emerald-500">
                        <template #icon>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                            </svg>
                        </template>
                    </StatCard>

                    <StatCard title="Общий доход"
                              :value="stats.total_income"
                              subtitle="За всё время"
                              iconBg="bg-fuchsia-600">
                        <template #icon>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                <path d="M12 2v20" />
                            </svg>
                        </template>
                    </StatCard>

                    <StatCard title="Мои треки"
                              :value="stats.tracks_count"
                              :subtitle="stats.tracks_sub"
                              iconBg="bg-blue-500">
                        <template #icon>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z" />
                            </svg>
                        </template>
                    </StatCard>

                    <StatCard title="Выплачено"
                              :value="stats.paid_out"
                              subtitle="Всего получено"
                              iconBg="bg-orange-500">
                        <template #icon>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                            </svg>
                        </template>
                    </StatCard>
                </div>

                <!-- График доходов -->
                <div class="mt-8">
                    <div class="rounded-[12px] p-6"
                         style="background-color: #1A1F2B; box-shadow: 0px 4px 6px rgba(0,0,0,0.3);">
                        <h2 class="mb-6 text-lg font-semibold" style="color: #F8FAFC;">Мои доходы по месяцам</h2>
                        <div class="h-[320px] w-full" v-if="revenueData">
                            <LineChart :chart-data="revenueData" />
                        </div>
                        <div v-else class="text-gray-500 text-center py-10">Нет данных</div>
                    </div>
                </div>

                <!-- Топ треков -->
                <div class="mt-10" v-if="topTracks.length">
                    <h2 class="mb-6 text-xl font-semibold" style="color: #F8FAFC;">Мои топ треки по доходу</h2>
                    <div class="rounded-[12px] p-6"
                         style="background-color: #1A1F2B; box-shadow: 0px 4px 6px rgba(0,0,0,0.3);">
                        <div class="space-y-4">
                            <div v-for="(track, i) in topTracks" :key="i"
                                 class="flex items-center gap-4">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-xs font-bold"
                                      :class="i < 3 ? 'bg-violet-500/20 text-violet-400' : 'text-slate-600'">
                                    {{ i + 1 }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-white">{{ track.title }}</p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-sm font-bold text-white">{{ track.revenue ? Number(track.revenue).toLocaleString('ru-RU') + ' ₽' : '—' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="mt-10 text-center text-gray-500">
                    Пока нет данных о доходах по трекам.
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
