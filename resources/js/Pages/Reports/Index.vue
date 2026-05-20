<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const periods = [
    { value: 'week', label: 'Неделя' },
    { value: 'month', label: 'Месяц' },
    { value: 'quarter', label: '3 мес' },
    { value: 'year', label: 'Год' },
]

const artists = ['Все артисты', 'Мария Светлова', 'Тёмный Бит', 'Эхо Ночи', 'Рок Волна']
const platforms = ['Все площадки', 'VK Music', 'Apple Music', 'Spotify', 'Яндекс.Музыка']

const selectedPeriod   = ref('month')
const selectedArtist   = ref('Все артисты')
const selectedPlatform = ref('Все площадки')

const stats = computed(() => ({ total: 660_400, average: 165_100, tracks: 4 }))
const fmt = (n) => n.toLocaleString('ru-RU') + ' ₽'

const reportRows = [
    { artist: 'Мария Светлова', track: 'Летняя Ночь',    platform: 'VK Music', revenue: 124_500, authorShare: 75, labelShare: 25 },
    { artist: 'Тёмный Бит',     track: 'Городские Огни',  platform: 'VK Music', revenue: 203_400, authorShare: 75, labelShare: 25 },
    { artist: 'Эхо Ночи',       track: 'Синтез Звука',    platform: 'VK Music', revenue: 187_300, authorShare: 70, labelShare: 30 },
    { artist: 'Рок Волна',      track: 'Рок Сердца',      platform: 'VK Music', revenue: 145_200, authorShare: 55, labelShare: 45 },
]
</script>

<template>
    <Head title="Отчёты" />
    <AuthenticatedLayout>
        <div class="p-6 md:p-10">
            <div class="mx-auto max-w-5xl">

                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                    <h1 class="text-3xl md:text-4xl font-bold text-white">Отчёты</h1>

                    <div class="flex items-center gap-3">
                        <button class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-900 bg-white shadow-lg shadow-slate-900/20 transition hover:bg-slate-200 hover:scale-[1.02] active:scale-[0.98]">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Скачать Excel
                        </button>

                        <button class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition shadow-lg shadow-violet-900/20 hover:opacity-90 hover:scale-[1.02] active:scale-[0.98]"
                                style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%)">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Скачать PDF
                        </button>
                    </div>
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
                            <div class="relative">
                                <select v-model="selectedPeriod" class="w-full appearance-none bg-[#0B0E14] border border-white/10 text-white rounded-xl px-4 py-3 pr-10 text-sm focus:outline-none focus:border-violet-500/50 hover:border-white/20 transition cursor-pointer">
                                    <option v-for="p in periods" :key="p.value" :value="p.value">{{ p.label }}</option>
                                </select>
                                <svg class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Артист</label>
                            <div class="relative">
                                <select v-model="selectedArtist" class="w-full appearance-none bg-[#0B0E14] border border-white/10 text-white rounded-xl px-4 py-3 pr-10 text-sm focus:outline-none focus:border-violet-500/50 hover:border-white/20 transition cursor-pointer">
                                    <option v-for="a in artists" :key="a" :value="a">{{ a }}</option>
                                </select>
                                <svg class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Площадка</label>
                            <div class="relative">
                                <select v-model="selectedPlatform" class="w-full appearance-none bg-[#0B0E14] border border-white/10 text-white rounded-xl px-4 py-3 pr-10 text-sm focus:outline-none focus:border-violet-500/50 hover:border-white/20 transition cursor-pointer">
                                    <option v-for="p in platforms" :key="p" :value="p">{{ p }}</option>
                                </select>
                                <svg class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6">
                    <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5 transition hover:bg-white/[0.03]">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="text-sm text-slate-400 mb-1">Общий доход</div>
                        <div class="text-2xl font-bold text-white">{{ fmt(stats.total) }}</div>
                    </div>

                    <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5 transition hover:bg-white/[0.03]">
                        <div class="w-12 h-12 rounded-xl bg-violet-500 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="text-sm text-slate-400 mb-1 leading-tight">Средний доход на трек</div>
                        <div class="text-2xl font-bold text-white">{{ fmt(stats.average) }}</div>
                    </div>

                    <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5 transition hover:bg-white/[0.03]">
                        <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="text-sm text-slate-400 mb-1">Треков в отчёте</div>
                        <div class="text-2xl font-bold text-white">{{ stats.tracks }}</div>
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
                            <tr v-for="row in reportRows" :key="row.track" class="border-b border-white/5 last:border-0 transition-colors hover:bg-white/[0.03]">
                                <td class="py-4 px-6 text-sm text-white">{{ row.artist }}</td>
                                <td class="py-4 px-6 text-sm text-white">{{ row.track }}</td>
                                <td class="py-4 px-6 text-sm text-slate-300 whitespace-nowrap">{{ row.platform }}</td>
                                <td class="py-4 px-6 text-sm font-bold text-white whitespace-nowrap text-right">{{ row.revenue.toLocaleString('ru-RU') }} ₽</td>
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
