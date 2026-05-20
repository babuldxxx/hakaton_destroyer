<script setup>
import { usePage, Head } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import StatCard from '@/Components/StatCard.vue'
import LineChart from '@/Components/Charts/LineChart.vue'
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user ?? { name: 'SoundERP Label' })

const today = new Intl.DateTimeFormat('ru-RU', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
}).format(new Date())

/* ─── Период и график ─── */
const selectedPeriod = ref('year')
const periods = [
    { key: 'week', label: 'Неделя' },
    { key: 'month', label: 'Месяц' },
    { key: 'quarter', label: '3 мес.' },
    { key: 'half', label: '6 мес.' },
    { key: 'year', label: 'Год' },
]

const revenueData = computed(() => {
    const common = {
        label: 'Доход',
        borderColor: '#7C3AED',
        backgroundColor: 'rgba(124, 58, 237, 0.08)',
        borderWidth: 3,
        pointBackgroundColor: '#7C3AED',
        pointBorderColor: '#0B0E14',
        pointBorderWidth: 2,
        pointRadius: 5,
        pointHoverRadius: 7,
        tension: 0.4,
        fill: true
    }
    switch (selectedPeriod.value) {
        case 'week':
            return { labels: ['Пн','Вт','Ср','Чт','Пт','Сб','Вс'], datasets: [{ ...common, data: [120000,190000,150000,220000,280000,350000,310000] }] }
        case 'month':
            return { labels: ['1','5','10','15','20','25','30'], datasets: [{ ...common, data: [450000,520000,480000,610000,580000,720000,680000] }] }
        case 'quarter':
            return { labels: ['Нед 1','Нед 2','Нед 3','Нед 4','Нед 5','Нед 6','Нед 7','Нед 8','Нед 9','Нед 10','Нед 11','Нед 12'], datasets: [{ ...common, data: [1200000,1350000,1400000,1550000,1800000,2000000,2200000,2150000,2600000,3000000,3500000,3800000] }] }
        case 'half':
            return { labels: ['Янв','Фев','Мар','Апр','Май','Июн'], datasets: [{ ...common, data: [2100000,2350000,2500000,2700000,2900000,3200000] }] }
        case 'year':
        default:
            return { labels: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'], datasets: [{ ...common, data: [2100000,2350000,2500000,2700000,2900000,3200000,3450000,3600000,3800000,3900000,4300000,4500000] }] }
    }
})

const platformData = {
    labels: ['VK Music', 'Apple Music', 'Яндекс.Музыка', 'Spotify', 'Другие'],
    datasets: [{ data: [5420000, 4130000, 2800000, 1537000, 600000], backgroundColor: ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#64748B'], borderColor: 'white', borderWidth: 2, hoverOffset: 4 }]
}

const platformLegend = [
    { name: 'VK Music', amount: '5 420 000 ₽', color: '#3B82F6' },
    { name: 'Apple Music', amount: '4 130 000 ₽', color: '#8B5CF6' },
    { name: 'Яндекс.Музыка', amount: '2 800 000 ₽', color: '#10B981' },
    { name: 'Spotify', amount: '1 537 000 ₽', color: '#F59E0B' },
    { name: 'Другие', amount: '600 000 ₽', color: '#64748B' },
]

const topTracks = [
    { title: 'Городские Огни', artist: 'Темный Бит', source: 'VK Music', amount: '203 400', growth: '+12%'},
    { title: 'Синтез Звука', artist: 'Эхо Ночи', source: 'Spotify', amount: '187 300', growth: '+8%'},
    { title: 'Вечерний Джаз', artist: 'Анна Вокал', source: 'Apple Music', amount: '156 700', growth: '+15%'},
    { title: 'Рок Сердца', artist: 'Рок Волна', source: 'VK Music', amount: '145 200', growth: '+5%'},
    { title: 'Потустороннее', artist: 'Магия Снов', source: 'Яндекс.Музыка', amount: '124 500', growth: '+10%'},
]
</script>

<template>
    <Head title="Дашборд лейбла" />
    <AuthenticatedLayout>
        <div class="p-6 md:p-10">
            <div class="mx-auto max-w-6xl">
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-[32px] font-bold leading-tight" style="color: #F8FAFC;">Здравствуйте, {{ user.name }}</h1>
                        <p class="mt-2 text-base" style="color: #94A3B8;">Сегодня: {{ today }}</p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-xl border-2" style="border-color: #7C3AED;">
                        <span class="flex h-full w-full items-center justify-center text-lg font-bold text-white" style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">L</span>
                    </div>
                </div>

                <!-- KPI Grid -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
                    <StatCard title="Общий доход" value="13 787 000 ₽" subtitle="+15.3% к прошлому году" iconBg="bg-emerald-500">
                        <template #icon>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>
                        </template>
                    </StatCard>
                    <StatCard title="Активные артисты" value="24" subtitle="+3 новых за месяц" iconBg="bg-violet-500">
                        <template #icon>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                        </template>
                    </StatCard>
                    <StatCard title="Треки в каталоге" value="156" subtitle="+12 за месяц" iconBg="bg-blue-500">
                        <template #icon>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z" /></svg>
                        </template>
                    </StatCard>
                    <StatCard title="Ожидают выплаты" value="532 000 ₽" subtitle="4 артиста" iconBg="bg-orange-500">
                        <template #icon>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </template>
                    </StatCard>
                </div>

                <!-- Charts -->
                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="rounded-[12px] p-6 lg:col-span-2" style="background-color: #1A1F2B; box-shadow: 0px 4px 6px rgba(0,0,0,0.3);">
                        <h2 class="mb-6 text-lg font-semibold" style="color: #F8FAFC;">Динамика доходов по месяцам</h2>
                        <div class="h-[320px] w-full"><LineChart :chart-data="revenueData" /></div>
                        <div class="mt-5 flex flex-wrap gap-2">
                            <button v-for="p in periods" :key="p.key" @click="selectedPeriod = p.key" class="px-3.5 py-1.5 rounded-full text-xs font-medium transition border" :class="selectedPeriod === p.key ? 'text-white border-transparent' : 'text-slate-400 border-slate-700 hover:text-white hover:border-slate-500'" :style="selectedPeriod === p.key ? { background: 'linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%)' } : {}">{{ p.label }}</button>
                        </div>
                    </div>
                    <div class="rounded-[12px] p-6" style="background-color: #1A1F2B; box-shadow: 0px 4px 6px rgba(0,0,0,0.3);">
                        <h2 class="mb-6 text-lg font-semibold" style="color: #F8FAFC;">Доходы по площадкам</h2>
                        <div class="h-[220px] w-full"><DoughnutChart :chart-data="platformData" /></div>
                        <div class="mt-6 space-y-2">
                            <div v-for="p in platformLegend" :key="p.name" class="flex items-center justify-between rounded-lg px-3 py-2" style="background-color: #0B0E14;">
                                <div class="flex items-center gap-2"><span class="h-3 w-3 rounded-full" :style="{ backgroundColor: p.color }"></span><span class="text-sm" style="color: #94A3B8;">{{ p.name }}</span></div>
                                <span class="text-sm font-semibold text-white">{{ p.amount }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Топ треков -->
                <div class="mt-10">
                    <h2 class="mb-6 text-xl font-semibold" style="color: #F8FAFC;">Топ-5 треков по доходу</h2>
                    <div class="rounded-[12px] p-6" style="background-color: #1A1F2B; box-shadow: 0px 4px 6px rgba(0,0,0,0.3);">
                        <div class="space-y-4">
                            <div v-for="(tr, i) in topTracks" :key="i" class="flex items-center gap-4">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-xs font-bold" :class="i < 3 ? 'bg-violet-500/20 text-violet-400' : 'text-slate-600'">{{ i + 1 }}</span>
                                <div class="min-w-0 flex-1"><p class="truncate text-sm font-semibold text-white">{{ tr.title }}</p><p class="truncate text-xs" style="color: #64748B;">{{ tr.artist }}</p></div>
                                <span class="hidden shrink-0 rounded-md px-2 py-1 text-[10px] font-bold uppercase tracking-wider sm:inline-block" :class="{'bg-blue-500/10 text-blue-400': tr.source === 'VK Music','bg-pink-500/10 text-pink-400': tr.source === 'Spotify','bg-violet-500/10 text-violet-400': tr.source === 'Apple Music','bg-emerald-500/10 text-emerald-400': tr.source === 'Яндекс.Музыка'}">{{ tr.source }}</span>
                                <div class="shrink-0 text-right"><p class="text-sm font-bold text-white">{{ tr.amount }} ₽</p><p class="text-xs font-medium text-emerald-400">{{ tr.growth }}</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
