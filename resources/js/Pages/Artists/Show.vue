<script setup>
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
    artist: Object,
})

const activeTab = ref('overview')
const tabs = [
    { key: 'overview', label: 'Обзор' },
    { key: 'tracks', label: 'Треки' },
    { key: 'finances', label: 'Доходы' },
]

/* ─── Период и график ─── */
const selectedPeriod = ref('year')

const periods = [
    { key: 'week', label: 'Неделя' },
    { key: 'month', label: 'Месяц' },
    { key: 'quarter', label: '3 мес.' },
    { key: 'half', label: '6 мес.' },
    { key: 'year', label: 'Год' },
]

const chartConf = computed(() => {
    switch (selectedPeriod.value) {
        case 'week':
            return { labels: ['Пн','Вт','Ср','Чт','Пт','Сб','Вс'], values: [12, 19, 15, 22, 28, 35, 31], max: 40 }
        case 'month':
            return { labels: ['1','5','10','15','20','25','30'], values: [45, 52, 48, 61, 58, 72, 68], max: 80 }
        case 'quarter':
            return { labels: ['Нед 1','Нед 2','Нед 3','Нед 4','Нед 5','Нед 6','Нед 7','Нед 8','Нед 9','Нед 10','Нед 11','Нед 12'], values: [120, 135, 140, 155, 180, 200, 220, 215, 260, 300, 350, 380], max: 400 }
        case 'half':
            return { labels: ['Янв','Фев','Мар','Апр','Май','Июн'], values: [180, 205, 220, 235, 284, 350], max: 400 }
        case 'year':
        default:
            return { labels: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'], values: [120, 135, 140, 155, 180, 200, 220, 215, 260, 300, 350, 380], max: 400 }
    }
})

const chartLabels = computed(() => chartConf.value.labels)
const chartValues = computed(() => chartConf.value.values)
const maxChart = computed(() => chartConf.value.max)
const yTicks = computed(() => {
    const step = maxChart.value / 5
    return Array.from({ length: 6 }, (_, i) => Math.round(i * step))
})

const W = 760, H = 260, pad = 40
const pw = W - pad * 2
const pH = H - pad * 2

const points = computed(() =>
    chartValues.value.map((v, i) => {
        const x = pad + (chartLabels.value.length > 1 ? i * (pw / (chartLabels.value.length - 1)) : pw / 2)
        const y = H - pad - (v / maxChart.value) * pH
        return `${x},${y}`
    }).join(' ')
)

const circles = computed(() =>
    chartValues.value.map((v, i) => ({
        x: pad + (chartLabels.value.length > 1 ? i * (pw / (chartLabels.value.length - 1)) : pw / 2),
        y: H - pad - (v / maxChart.value) * pH
    }))
)

const hoveredIndex = ref(null)

const tooltip = computed(() => {
    if (hoveredIndex.value === null) return null
    const i = hoveredIndex.value
    return {
        x: circles.value[i].x,
        y: circles.value[i].y,
        label: chartLabels.value[i],
        value: chartValues.value[i] * 1000
    }
})
</script>

<template>
    <Head :title="artist.stage_name" />

    <AuthenticatedLayout>
        <div class="p-6 md:p-10">
            <div class="mx-auto max-w-5xl">

                <!-- Назад -->
                <Link :href="route('artists.index')" class="inline-flex items-center gap-2 text-slate-400 hover:text-white transition mb-6">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Назад к артистам
                </Link>

                <!-- Профиль -->
                <div class="bg-[#1A1F2B] rounded-[20px] p-6 md:p-8 mb-8">
                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <div class="w-32 h-32 md:w-40 md:h-40 rounded-2xl p-1 shrink-0"
                            style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                            <div class="w-full h-full rounded-xl bg-[#0B0E14] flex items-center justify-center text-5xl font-bold text-white"
                                style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                                {{ artist.stage_name?.charAt(0) ?? 'A' }}
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <h1 class="text-3xl md:text-4xl font-bold text-white mb-3">{{ artist.stage_name }}</h1>
                            <p class="text-slate-400 mb-4">{{ artist.bio ?? 'Нет описания' }}</p>
                            <p class="text-slate-400 mb-4">{{ artist.real_name }}</p>
                            <p class="text-slate-400 mb-6">{{ artist.user?.email ?? '—' }}</p>

                            <div class="flex gap-8">
                                <div>
                                    <div class="text-sm text-slate-400 mb-1">Треков</div>
                                    <div class="text-3xl font-bold text-white">{{ artist.songs?.length ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Вкладки -->
                <div class="inline-flex bg-[#1A1F2B] rounded-full p-1 mb-8">
                    <button
                        v-for="t in tabs"
                        :key="t.key"
                        @click="activeTab = t.key"
                        class="px-5 py-2 rounded-full text-sm font-medium transition"
                        :class="activeTab === t.key ? 'text-white' : 'text-slate-400 hover:text-white'"
                        :style="activeTab === t.key ? { background: 'linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%)' } : {}"
                    >
                        {{ t.label }}
                    </button>
                </div>

                <!-- Обзор -->
                <div v-if="activeTab === 'overview'" class="space-y-6">
                    <div class="bg-[#1A1F2B] rounded-[20px] p-6">
                        <h2 class="text-lg font-semibold text-white mb-6">Доходы по месяцам</h2>
                        <div class="w-full overflow-x-auto">
                            <svg viewBox="0 0 760 260" class="w-full h-auto min-w-[600px]">
                                <g class="text-slate-700">
                                    <line v-for="tick in yTicks" :key="'h'+tick"
                                        :x1="pad" :x2="W-pad"
                                        :y1="H - pad - (tick/maxChart)*pH"
                                        :y2="H - pad - (tick/maxChart)*pH"
                                        stroke="currentColor" stroke-width="1" stroke-dasharray="4 4" />
                                    <text v-for="tick in yTicks" :key="'yl'+tick"
                                        :x="pad-10" :y="H - pad - (tick/maxChart)*pH + 4"
                                        text-anchor="end" fill="currentColor" font-size="11">{{ tick }}K</text>
                                </g>
                                <g class="text-slate-500">
                                    <text v-for="(l,i) in chartLabels" :key="'xl'+i"
                                        :x="pad + (chartLabels.length > 1 ? i*(pw/(chartLabels.length-1)) : pw/2)" :y="H-12"
                                        text-anchor="middle" fill="currentColor" font-size="11">{{ l }}</text>
                                </g>
                                <polygon :points="`${pad},${H-pad} ${points} ${W-pad},${H-pad}`"
                                    fill="rgba(124,58,237,0.06)" />
                                <polyline :points="points" fill="none" stroke="#7C3AED" stroke-width="3"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <circle v-for="(c,i) in circles" :key="'dot'+i"
                                    :cx="c.x" :cy="c.y"
                                    :r="hoveredIndex === i ? 7 : 5"
                                    fill="#7C3AED" stroke="#0B0E14" stroke-width="2"
                                    class="cursor-pointer"
                                    @mouseenter="hoveredIndex = i"
                                    @mouseleave="hoveredIndex = null" />
                                <g v-if="tooltip" pointer-events="none">
                                    <circle :cx="tooltip.x" :cy="tooltip.y" r="10" fill="#7C3AED" opacity="0.25" />
                                    <rect :x="tooltip.x - 70" :y="tooltip.y - 78" width="140" height="50" rx="8"
                                        fill="#1e293b" stroke="#334155" stroke-width="1" />
                                    <text :x="tooltip.x" :y="tooltip.y - 58" text-anchor="middle" fill="white" font-size="13" font-weight="600">{{ tooltip.label }}</text>
                                    <text :x="tooltip.x" :y="tooltip.y - 42" text-anchor="middle" fill="#94a3b8" font-size="11">{{ tooltip.value.toLocaleString('ru-RU') }} ₽</text>
                                </g>
                            </svg>
                        </div>
                        <div class="mt-5 flex flex-wrap gap-2">
                            <button v-for="p in periods" :key="p.key" @click="selectedPeriod = p.key"
                                class="px-3.5 py-1.5 rounded-full text-xs font-medium transition border"
                                :class="selectedPeriod === p.key ? 'text-white border-transparent' : 'text-slate-400 border-slate-700 hover:text-white hover:border-slate-500'"
                                :style="selectedPeriod === p.key ? { background: 'linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%)' } : {}">
                                {{ p.label }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Треки -->
                <div v-if="activeTab === 'tracks'" class="bg-[#1A1F2B] rounded-[20px] p-6">
                    <h2 class="text-lg font-semibold text-white mb-6">Треки артиста</h2>
                    <div v-if="!artist.songs?.length" class="text-slate-400 text-center py-10">
                        Нет треков.
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-white/5">
                                    <th class="pb-3 text-sm font-medium text-slate-400 pr-4">Название</th>
                                    <th class="pb-3 text-sm font-medium text-slate-400 pr-4">Дата релиза</th>
                                    <th class="pb-3 text-sm font-medium text-slate-400 pr-4">Жанр</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="song in artist.songs" :key="song.id" class="border-b border-white/5 last:border-0">
                                    <td class="py-4 text-sm text-white pr-4">{{ song.title }}</td>
                                    <td class="py-4 text-sm text-slate-400 pr-4">{{ song.released_at ?? '—' }}</td>
                                    <td class="py-4 text-sm text-slate-400 pr-4">{{ song.genre?.name ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Доходы -->
                <div v-if="activeTab === 'finances'" class="bg-[#1A1F2B] rounded-[20px] p-6">
                    <h2 class="text-lg font-semibold text-white mb-6">Доходы по площадкам</h2>
                    <div class="text-slate-400 text-center py-10">
                        Данные о доходах будут доступны позже.
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
