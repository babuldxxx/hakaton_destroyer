<script setup>
import { ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    stats: Object,
    artists: Array,
    platforms: Array,
    periods: Array,      // ← строки Y-m из БД
    reportRows: Array,
    filters: Object,
})

// Переименовано, чтобы не конфликтовать с props.periods
const periodOptions = [
    { value: 'all', label: 'Всё время' },
    { value: 'week', label: 'Неделя' },
    { value: 'month', label: 'Месяц' },
    { value: 'quarter', label: 'Квартал' },
    { value: 'year', label: 'Год' },
]

const selectedPeriod   = ref(props.filters?.period   ?? 'all')
const selectedArtist   = ref(props.filters?.artist   ?? 'all')
const selectedPlatform = ref(props.filters?.platform ?? 'all')

const fmt = (val) => {
    const n = Number(val ?? 0)
    return (isNaN(n) ? 0 : n).toLocaleString('ru-RU') + ' ₽'
}

function applyFilters() {
    router.get(route('reports.index'), {
        period:   selectedPeriod.value,
        artist:   selectedArtist.value,
        platform: selectedPlatform.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

// Импорт CSV
const importForm = useForm({ file: null })
const fileInput  = ref(null)

function submitImport() {
    importForm.post(route('reports.import'), {
        preserveScroll: true,
        onSuccess: () => {
            importForm.reset()
            if (fileInput.value) fileInput.value.value = ''
        },
    })
}
</script>

<template>
    <Head title="Отчёты" />
    <AuthenticatedLayout>
        <div class="p-6 md:p-10">
            <div class="mx-auto max-w-5xl space-y-6">

                <h1 class="text-3xl md:text-4xl font-bold text-white">Отчёты</h1>

                <!-- Flash messages -->
                <div v-if="$page.props.flash?.success" class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl text-sm">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error" class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl text-sm">
                    {{ $page.props.flash.error }}
                </div>

                <!-- Filters + Import -->
                <div class="bg-[#1A1F2B] rounded-2xl p-6 md:p-8 border border-white/5">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <h2 class="text-lg font-semibold text-white">Формирование отчёта</h2>
                        </div>

                        <form @submit.prevent="submitImport" class="flex items-center gap-3">
                            <input
                                ref="fileInput"
                                type="file"
                                accept=".csv,.txt"
                                @input="importForm.file = $event.target.files[0]"
                                class="block w-48 text-xs text-gray-300
                                     file:mr-3 file:py-1.5 file:px-3 file:rounded-lg
                                     file:border-0 file:text-xs file:font-medium
                                     file:bg-gray-700 file:text-white hover:file:bg-gray-600
                                     bg-gray-800 border border-gray-700 rounded-lg cursor-pointer"
                            />
                            <button
                                type="submit"
                                :disabled="importForm.processing || !importForm.file"
                                class="px-4 py-2 bg-violet-600 hover:bg-violet-500 disabled:bg-gray-700 rounded-lg text-sm font-medium transition"
                            >
                                {{ importForm.processing ? '...' : 'Импорт CSV' }}
                            </button>
                        </form>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Период</label>
                            <select v-model="selectedPeriod" @change="applyFilters"
                                class="w-full bg-[#0B0E14] border border-white/10 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-violet-500/50 hover:border-white/20 transition">
                                
                                <option v-for="opt in periodOptions" :key="opt.value" :value="opt.value">
                                    {{ opt.label }}
                                </option>
                                
                                <optgroup v-if="props.periods.length" label="─ Загруженные периоды ─">
                                    <option v-for="p in props.periods" :key="p" :value="p">{{ p }}</option>
                                </optgroup>
                            </select>
                        </div>  

                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Артист</label>
                            <select v-model="selectedArtist" @change="applyFilters"
                                class="w-full bg-[#0B0E14] border border-white/10 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-violet-500/50 hover:border-white/20 transition">
                                <option value="all">Все артисты</option>
                                <option v-for="a in artists" :key="a.id" :value="a.id">{{ a.stage_name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Площадка</label>
                            <select v-model="selectedPlatform" @change="applyFilters"
                                class="w-full bg-[#0B0E14] border border-white/10 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-violet-500/50 hover:border-white/20 transition">
                                <option value="all">Все площадки</option>
                                <option v-for="p in platforms" :key="p.id" :value="p.id">{{ p.name }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Stats — 4 карточки -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                    <!-- Общий доход -->
                    <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-6h6" />
                            </svg>
                        </div>
                        <div class="text-sm text-slate-400 mb-1">Общий доход</div>
                        <div class="text-2xl font-bold text-white">{{ fmt(stats?.total) }}</div>
                    </div>

                    <!-- Чистый доход лейбла (NEW) -->
                    <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5">
                        <div class="w-12 h-12 rounded-xl bg-sky-500 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="text-sm text-slate-400 mb-1">Чистый доход лейбла</div>
                        <div class="text-2xl font-bold text-white">{{ fmt(stats?.labelTotal) }}</div>
                    </div>

                    <!-- Средний доход на трек -->
                    <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5">
                        <div class="w-12 h-12 rounded-xl bg-violet-500 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="text-sm text-slate-400 mb-1">Средний доход на трек</div>
                        <div class="text-2xl font-bold text-white">{{ fmt(stats?.average) }}</div>
                    </div>

                    <!-- Треков в отчёте -->
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

                <!-- Table -->
                <div class="bg-[#1A1F2B] rounded-2xl border border-white/5 overflow-hidden">
                    <div class="p-6 border-b border-white/5 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-white">Предпросмотр отчёта</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-white/5 text-slate-400 text-sm">
                                    <th class="py-4 px-6 font-medium">Артист</th>
                                    <th class="py-4 px-6 font-medium">Трек</th>
                                    <th class="py-4 px-6 font-medium whitespace-nowrap">Площадка</th>
                                    <th class="py-4 px-6 font-medium whitespace-nowrap">Период</th>
                                    <th class="py-4 px-6 font-medium text-right">Всего</th>
                                    <th class="py-4 px-6 font-medium text-right">Артистам</th>
                                    <th class="py-4 px-6 font-medium text-right">Лейблу</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!reportRows?.length">
                                    <td colspan="7" class="py-8 text-center text-sm text-slate-500">
                                        Нет данных по выбранным фильтрам.
                                    </td>
                                </tr>
                                <tr v-for="(row, idx) in reportRows" :key="idx"
                                    class="border-b border-white/5 last:border-0 hover:bg-white/[0.03]">
                                    <td class="py-4 px-6 text-sm text-white">{{ row.artist }}</td>
                                    <td class="py-4 px-6 text-sm text-white">{{ row.track }}</td>
                                    <td class="py-4 px-6 text-sm text-slate-300 whitespace-nowrap">{{ row.platform }}</td>
                                    <td class="py-4 px-6 text-sm text-slate-400 whitespace-nowrap">{{ row.period }}</td>
                                    <td class="py-4 px-6 text-sm font-bold text-white text-right whitespace-nowrap">
                                        {{ fmt(row.revenue) }}
                                    </td>
                                    <td class="py-4 px-6 text-sm font-semibold text-emerald-400 text-right whitespace-nowrap">
                                        {{ fmt(row.artistAmount) }}
                                        <div class="text-xs text-slate-500 mt-0.5">
                                            <span v-if="row.artistShares">{{ row.artistShares }}%</span>
                                            <span v-else>{{ row.authorShare }}%</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-sm font-semibold text-amber-400 text-right whitespace-nowrap">
                                        {{ fmt(row.labelAmount) }}
                                        <span class="text-xs text-slate-500 ml-1">{{ row.labelShare }}%</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>