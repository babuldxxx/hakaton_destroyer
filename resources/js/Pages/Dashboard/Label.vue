<script setup>
import { usePage, Head, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import StatCard from '@/Components/StatCard.vue'
import LineChart from '@/Components/Charts/LineChart.vue'
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue'

const props = defineProps({
    stats: Object,
    revenueData: Object,
    platformData: Object,
    topTracks: Array,
    filters: Object,
})

const page = usePage()
const user = computed(() => page.props.auth?.user ?? { name: 'SoundERP Label' })

const today = new Intl.DateTimeFormat('ru-RU', {
    day: 'numeric', month: 'long', year: 'numeric'
}).format(new Date())

const selectedPeriod = ref(props.filters?.period ?? 'year')
const periods = [
    { key: 'week', label: 'Неделя' },
    { key: 'month', label: 'Месяц' },
    { key: 'quarter', label: '3 мес.' },
    { key: 'half', label: '6 мес.' },
    { key: 'year', label: 'Год' },
]

function changePeriod(key) {
    selectedPeriod.value = key
    router.get(route('label.dashboard'), { period: key }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}
</script>

<template>
    <Head title="Дашборд лейбла" />
    <AuthenticatedLayout>
        <div class="p-6 md:p-10">
            <div class="mx-auto max-w-6xl">
                <!-- Хедер -->
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-[32px] font-bold leading-tight" style="color: #F8FAFC;">
                            Здравствуйте, {{ user.name }}
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
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
                    <StatCard title="Общий доход"
                              :value="stats.totalRevenue"
                              subtitle="за текущий год"
                              iconBg="bg-emerald-500">
                        <template #icon>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                            </svg>
                        </template>
                    </StatCard>
                    <StatCard title="Активные артисты"
                              :value="stats.artistsCount"
                              :subtitle="'артистов в лейбле'"
                              iconBg="bg-violet-500">
                        <template #icon>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                        </template>
                    </StatCard>
                    <StatCard title="Треки в каталоге"
                              :value="stats.tracksCount"
                              :subtitle="'треков'"
                              iconBg="bg-blue-500">
                        <template #icon>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z" />
                            </svg>
                        </template>
                    </StatCard>
                    <StatCard title="Ожидают выплаты"
                              :value="stats.pendingPayouts"
                              subtitle="к выплате"
                              iconBg="bg-orange-500">
                        <template #icon>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                    </StatCard>
                </div>

                <!-- Charts -->
                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="rounded-[12px] p-6 lg:col-span-2"
                         style="background-color: #1A1F2B; box-shadow: 0px 4px 6px rgba(0,0,0,0.3);">
                        <h2 class="mb-6 text-lg font-semibold" style="color: #F8FAFC;">Динамика доходов</h2>
                        <div class="h-[320px] w-full">
                            <LineChart :chart-data="revenueData" />
                        </div>
                        <!-- Переключатель периода -->
                        <div class="mt-5 flex flex-wrap gap-2">
                            <button
                                v-for="p in periods"
                                :key="p.key"
                                @click="changePeriod(p.key)"
                                class="px-3.5 py-1.5 rounded-full text-xs font-medium transition border"
                                :class="selectedPeriod === p.key
                  ? 'text-white border-transparent'
                  : 'text-slate-400 border-slate-700 hover:text-white hover:border-slate-500'"
                                :style="selectedPeriod === p.key
                  ? { background: 'linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%)' }
                  : {}"
                            >
                                {{ p.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Donut -->
                    <div class="rounded-[12px] p-6"
                         style="background-color: #1A1F2B; box-shadow: 0px 4px 6px rgba(0,0,0,0.3);">
                        <h2 class="mb-6 text-lg font-semibold" style="color: #F8FAFC;">Доходы по площадкам</h2>
                        <div class="h-[220px] w-full" v-if="platformData.labels.length">
                            <DoughnutChart :chart-data="platformData" />
                        </div>
                        <p v-else class="text-gray-500 text-sm text-center py-10">Нет данных</p>
                        <div class="mt-6 space-y-2" v-if="platformData.labels.length">
                            <div v-for="(label, idx) in platformData.labels" :key="label"
                                 class="flex items-center justify-between rounded-lg px-3 py-2"
                                 style="background-color: #0B0E14;">
                                <div class="flex items-center gap-2">
                                    <span class="h-3 w-3 rounded-full"
                                          :style="{ backgroundColor: platformData.datasets[0].backgroundColor[idx] }"></span>
                                    <span class="text-sm" style="color: #94A3B8;">{{ label }}</span>
                                </div>
                                <span class="text-sm font-semibold text-white">
                                    {{ Number(platformData.datasets[0].data[idx]).toLocaleString('ru-RU') }} ₽
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Топ-5 треков -->
                <div class="mt-10" v-if="topTracks.length">
                    <h2 class="mb-6 text-xl font-semibold" style="color: #F8FAFC;">Топ-5 треков по доходу</h2>
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
                                    <p class="truncate text-xs" style="color: #64748B;">{{ track.artist }}</p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-sm font-bold text-white">{{ track.amount }}</p>
                                    <p class="text-xs font-medium text-emerald-400">{{ track.growth }}</p>
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
