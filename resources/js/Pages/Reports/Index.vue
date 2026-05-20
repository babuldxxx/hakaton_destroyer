<script setup>
import { ref, computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    stats: Object,
    artists: Array,
    platforms: Array,
    reportRows: Array,
    filters: Object,
})

const page = usePage()

const periods = [
    { value: 'week', label: 'Неделя' },
    { value: 'month', label: 'Месяц' },
    { value: 'quarter', label: '3 мес' },
    { value: 'year', label: 'Год' },
]

const selectedPeriod = ref(props.filters?.period ?? 'month')
const selectedArtist = ref(props.filters?.artist ?? 'all')
const selectedPlatform = ref(props.filters?.platform ?? 'all')

const fmt = (n) => Number(n).toLocaleString('ru-RU') + ' ₽'

function applyFilters() {
    router.get(route('reports'), {
        period: selectedPeriod.value,
        artist: selectedArtist.value,
        platform: selectedPlatform.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}
</script>

<template>
    <Head title="Отчёты" />
    <AuthenticatedLayout>
        <div class="p-6 md:p-10">
            <div class="mx-auto max-w-5xl">

                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                    <h1 class="text-3xl md:text-4xl font-bold text-white">Отчёты</h1>
                </div>

                <!-- Filters -->
                <div class="bg-[#1A1F2B] rounded-2xl p-6 md:p-8 border border-white/5 mb-6">
                    <div class="flex items-center gap-3 mb-6">
                        <svg class="w-6 h-6 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <h2 class="text-lg font-semibold text-white">Формирование отчёта</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Период</label>
                            <select
                                v-model="selectedPeriod"
                                @change="applyFilters"
                                class="w-full appearance-none bg-[#0B0E14] border border-white/10 text-white rounded-xl px-4 py-3 pr-10 text-sm focus:outline-none focus:border-violet-500/50 hover:border-white/20 transition cursor-pointer"
                            >
                                <option v-for="p in periods" :key="p.value" :value="p.value">{{ p.label }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Артист</label>
                            <select
                                v-model="selectedArtist"
                                @change="applyFilters"
                                class="w-full appearance-none bg-[#0B0E14] border border-white/10 text-white rounded-xl px-4 py-3 pr-10 text-sm focus:outline-none focus:border-violet-500/50 hover:border-white/20 transition cursor-pointer"
                            >
                                <option value="all">Все артисты</option>
                                <option v-for="a in artists" :key="a.id" :value="a.id">{{ a.stage_name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Площадка</label>
                            <select
                                v-model="selectedPlatform"
                                @change="applyFilters"
                                class="w-full appearance-none bg-[#0B0E14] border border-white/10 text-white rounded-xl px-4 py-3 pr-10 text-sm focus:outline-none focus:border-violet-500/50 hover:border-white/20 transition cursor-pointer"
                            >
                                <option value="all">Все площадки</option>
                                <option v-for="p in platforms" :key="p.id" :value="p.id">{{ p.name }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Stats cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6">
                    <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="text-sm text-slate-400 mb-1">Общий доход</div>
                        <div class="text-2xl font-bold text-white">{{ fmt(stats?.total ?? 0) }}</div>
                    </div>

                    <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5">
                        <div class="w-12 h-12 rounded-xl bg-violet-500 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="text-sm text-slate-400 mb-1 leading-tight">Средний доход на трек</div>
                        <div class="text-2xl font-bold text-white">{{ fmt(stats?.average ?? 0) }}</div>
                    </div>

                    <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5">
                        <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="text-sm text-slate-400 mb-1">Треков в отчёте</div>
                        <div class="text-2xl font-bold text-white">{{ stats?.tracks ?? 0 }}</div>
                    </div>
                </div>

                <!-- Report Preview -->
                <div class="bg-[#1A1F2B] rounded-2xl border border-white/5 mt-6">
                    <div class="p-6 border-b border-white/5">
                        <h2 class="text-lg font-semibold text-white">Предпросмотр отчёта</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                            <tr class="border-b border-white/5 text-slate-400 text-sm">
                                <th class="py-4 px-6 font-medium">Артист</th>
                                <th class="py-4 px-6 font-medium">Трек</th>
                                <th class="py-4 px-6 font-medium whitespace-nowrap">Площадка</th>
                                <th class="py-4 px-6 font-medium whitespace-nowrap text-right">Доход</th>
                                <th class="py-4 px-6 font-medium whitespace-nowrap text-right">Доля авторов</th>
                                <th class="py-4 px-6 font-medium whitespace-nowrap text-right">Доля лейбла</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-if="!reportRows?.length">
                                <td colspan="6" class="py-8 text-center text-sm text-slate-500">Нет данных по выбранным фильтрам.</td>
                            </tr>
                            <tr v-for="row in reportRows" :key="row.track + row.platform" class="border-b border-white/5 last:border-0 transition-colors hover:bg-white/[0.03]">
                                <td class="py-4 px-6 text-sm text-white">{{ row.artist }}</td>
                                <td class="py-4 px-6 text-sm text-white">{{ row.track }}</td>
                                <td class="py-4 px-6 text-sm text-slate-300 whitespace-nowrap">{{ row.platform }}</td>
                                <td class="py-4 px-6 text-sm font-bold text-white whitespace-nowrap text-right">{{ fmt(row.revenue) }}</td>
                                <td class="py-4 px-6 text-sm font-semibold text-emerald-400 whitespace-nowrap text-right">{{ row.authorShare }}%</td>
                                <td class="py-4 px-6 text-sm font-semibold text-amber-400 whitespace-nowrap text-right">{{ row.labelShare }}%</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
