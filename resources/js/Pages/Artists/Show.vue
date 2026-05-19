<script setup>
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import LabelLayout from '@/Layouts/LabelLayout.vue'

const props = defineProps({
  id: { type: Number, default: 1 }
})

const artistsDB = [
  {
    id: 1,
    name: 'Мария Светлова',
    description: 'Популярная поп-исполнительница с миллионами прослушиваний',
    genre: 'Поп',
    avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=400&fit=crop&crop=face',
    totalRevenue: 524300,
    tracksCount: 12,
    gradFrom: '#7C3AED',
    gradTo: '#3B82F6',
  },
  {
    id: 2,
    name: 'Тёмный Бит',
    description: 'Мрачный хип-хоп с глубоким басом и уличной атмосферой',
    genre: 'Хип-хоп',
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face',
    totalRevenue: 833000,
    tracksCount: 24,
    gradFrom: '#EC4899',
    gradTo: '#BE185D',
  },
  {
    id: 3,
    name: 'Эхо Ночи',
    description: 'Атмосферная электроника для ночных дорог и клубов',
    genre: 'Электронная',
    avatar: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=400&h=400&fit=crop&crop=face',
    totalRevenue: 312500,
    tracksCount: 8,
    gradFrom: '#10B981',
    gradTo: '#059669',
  },
  {
    id: 4,
    name: 'Рок Волна',
    description: 'Энергичный инди-рок с мощными гитарными риффами',
    genre: 'Рок',
    avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop&crop=face',
    totalRevenue: 445000,
    tracksCount: 15,
    gradFrom: '#F59E0B',
    gradTo: '#D97706',
  },
]

const artist = computed(() => artistsDB.find(a => a.id === props.id) ?? artistsDB[0])

const activeTab = ref('overview')
const tabs = [
  { key: 'overview', label: 'Обзор' },
  { key: 'tracks', label: 'Треки' },
  { key: 'finances', label: 'Доходы' },
]

/* ─── Период и график «Доходы» ─── */
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
      return {
        labels: ['Пн','Вт','Ср','Чт','Пт','Сб','Вс'],
        values: [12, 19, 15, 22, 28, 35, 31],
        max: 40
      }
    case 'month':
      return {
        labels: ['1','5','10','15','20','25','30'],
        values: [45, 52, 48, 61, 58, 72, 68],
        max: 80
      }
    case 'quarter':
      return {
        labels: ['Нед 1','Нед 2','Нед 3','Нед 4','Нед 5','Нед 6','Нед 7','Нед 8','Нед 9','Нед 10','Нед 11','Нед 12'],
        values: [120, 135, 140, 155, 180, 200, 220, 215, 260, 300, 350, 380],
        max: 400
      }
    case 'half':
      return {
        labels: ['Янв','Фев','Мар','Апр','Май','Июн'],
        values: [180, 205, 220, 235, 284, 350],
        max: 400
      }
    case 'year':
    default:
      return {
        labels: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
        values: [120, 135, 140, 155, 180, 200, 220, 215, 260, 300, 350, 380],
        max: 400
      }
  }
})

const chartLabels = computed(() => chartConf.value.labels)
const chartValues = computed(() => chartConf.value.values)
const maxChart   = computed(() => chartConf.value.max)
const yTicks     = computed(() => {
  const step = maxChart.value / 5
  return Array.from({ length: 6 }, (_, i) => Math.round(i * step))
})

/* ─── SVG geometry ─── */
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

/* ─── Tooltip ─── */
const hoveredIndex = ref(null)

const tooltip = computed(() => {
  if (hoveredIndex.value === null) return null
  const i = hoveredIndex.value
  return {
    x: circles.value[i].x,
    y: circles.value[i].y,
    label: chartLabels.value[i],
    value: chartValues.value[i] * 1000 // показываем полную сумму
  }
})

/* ─── Табы: данные ─── */
const transactions = [
  { date: '2025-06-15', track: 'Летняя Ночь', platform: 'VK Music', amount: 124500 },
  { date: '2025-06-14', track: 'Городские Огни', platform: 'Apple Music', amount: 89300 },
  { date: '2025-06-12', track: 'Рассвет', platform: 'Spotify', amount: 67000 },
  { date: '2025-06-10', track: 'Танцевальный Ритм', platform: 'Яндекс.Музыка', amount: 45200 },
]

const tracks = [
  { title: 'Летняя Ночь', album: 'Сингл', date: '2025-06-01', platform: 'Все площадки', revenue: 124500 },
  { title: 'Городские Огни', album: 'Мегаполис', date: '2025-05-15', platform: 'Все площадки', revenue: 89300 },
  { title: 'Рассвет', album: 'Сингл', date: '2025-05-01', platform: 'Все площадки', revenue: 67000 },
  { title: 'Танцевальный Ритм', album: 'Вечеринка', date: '2025-04-20', platform: 'Все площадки', revenue: 45200 },
  { title: 'Мечты', album: 'Сингл', date: '2025-04-01', platform: 'Все площадки', revenue: 32100 },
]

const incomes = [
  { period: 'Июнь 2025', platform: 'VK Music', amount: 145000, status: 'Выплачено' },
  { period: 'Июнь 2025', platform: 'Apple Music', amount: 98000, status: 'Выплачено' },
  { period: 'Июнь 2025', platform: 'Spotify', amount: 76000, status: 'Выплачено' },
  { period: 'Май 2025', platform: 'VK Music', amount: 132000, status: 'Выплачено' },
  { period: 'Май 2025', platform: 'Яндекс.Музыка', amount: 87000, status: 'Выплачено' },
]
</script>

<template>
  <Head :title="artist.name" />
  <LabelLayout>
    <div class="p-6 md:p-10">
      <div class="mx-auto max-w-5xl">

        <!-- Back -->
        <Link href="/label/artists" class="inline-flex items-center gap-2 text-slate-400 hover:text-white transition mb-6">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
          </svg>
          Назад к артистам
        </Link>

        <!-- Profile card -->
        <div class="bg-[#1A1F2B] rounded-[20px] p-6 md:p-8 mb-8">
          <div class="flex flex-col md:flex-row gap-6 items-start">
            <!-- Avatar -->
            <div class="w-32 h-32 md:w-40 md:h-40 rounded-2xl p-1 shrink-0"
              :style="{ background: `linear-gradient(135deg, ${artist.gradFrom} 0%, ${artist.gradTo} 100%)` }">
              <img :src="artist.avatar" :alt="artist.name" class="w-full h-full object-cover rounded-xl bg-[#0B0E14]" />
            </div>

            <!-- Info -->
            <div class="flex-1 min-w-0">
              <h1 class="text-3xl md:text-4xl font-bold text-white mb-3">{{ artist.name }}</h1>
              <p class="text-slate-400 mb-4 max-w-xl">{{ artist.description }}</p>

              <span class="inline-block px-4 py-1 rounded-full text-sm font-medium mb-6"
                :class="{
                  'bg-violet-500/10 text-violet-400': artist.genre === 'Поп',
                  'bg-pink-500/10 text-pink-400': artist.genre === 'Хип-хоп',
                  'bg-emerald-500/10 text-emerald-400': artist.genre === 'Электронная',
                  'bg-orange-500/10 text-orange-400': artist.genre === 'Рок',
                  'bg-blue-500/10 text-blue-400': artist.genre === 'Джаз',
                }">
                {{ artist.genre }}
              </span>

              <div class="flex gap-8">
                <div>
                  <div class="text-sm text-slate-400 mb-1">Общий доход</div>
                  <div class="text-3xl font-bold text-white">{{ artist.totalRevenue.toLocaleString('ru-RU') }} ₽</div>
                </div>
                <div>
                  <div class="text-sm text-slate-400 mb-1">Треков</div>
                  <div class="text-3xl font-bold text-white">{{ artist.tracksCount }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabs -->
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

        <!-- TAB: Overview -->
        <div v-if="activeTab === 'overview'" class="space-y-6">
          <!-- Chart -->
          <div class="bg-[#1A1F2B] rounded-[20px] p-6">
            <h2 class="text-lg font-semibold text-white mb-6">Доходы по месяцам</h2>
            <div class="w-full overflow-x-auto">
              <svg viewBox="0 0 760 260" class="w-full h-auto min-w-[600px]">
                <!-- Grid lines + Y labels -->
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

                <!-- X labels -->
                <g class="text-slate-500">
                  <text v-for="(l,i) in chartLabels" :key="'xl'+i"
                    :x="pad + (chartLabels.length > 1 ? i*(pw/(chartLabels.length-1)) : pw/2)" :y="H-12"
                    text-anchor="middle" fill="currentColor" font-size="11">{{ l }}</text>
                </g>

                <!-- Area under curve -->
                <polygon :points="`${pad},${H-pad} ${points} ${W-pad},${H-pad}`"
                  fill="rgba(124,58,237,0.06)" />

                <!-- Line -->
                <polyline :points="points" fill="none" stroke="#7C3AED" stroke-width="3"
                  stroke-linecap="round" stroke-linejoin="round" />

                <!-- Dots -->
                <circle v-for="(c,i) in circles" :key="'dot'+i"
                  :cx="c.x" :cy="c.y"
                  :r="hoveredIndex === i ? 7 : 5"
                  fill="#7C3AED" stroke="#0B0E14" stroke-width="2"
                  class="cursor-pointer"
                  @mouseenter="hoveredIndex = i"
                  @mouseleave="hoveredIndex = null"
                />

                <!-- Tooltip -->
                <g v-if="tooltip" pointer-events="none">
                  <!-- glow behind dot -->
                  <circle :cx="tooltip.x" :cy="tooltip.y" r="10" fill="#7C3AED" opacity="0.25" />
                  
                  <!-- card -->
                  <rect
                    :x="tooltip.x - 70"
                    :y="tooltip.y - 78"
                    width="140"
                    height="50"
                    rx="8"
                    fill="#1e293b"
                    stroke="#334155"
                    stroke-width="1"
                  />
                  <text
                    :x="tooltip.x"
                    :y="tooltip.y - 58"
                    text-anchor="middle"
                    fill="white"
                    font-size="13"
                    font-weight="600"
                  >{{ tooltip.label }}</text>
                  <text
                    :x="tooltip.x"
                    :y="tooltip.y - 42"
                    text-anchor="middle"
                    fill="#94a3b8"
                    font-size="11"
                  >{{ tooltip.value.toLocaleString('ru-RU') }} ₽</text>
                </g>
              </svg>
            </div>

            <!-- Переключатель периода -->
            <div class="mt-5 flex flex-wrap gap-2">
              <button
                v-for="p in periods"
                :key="p.key"
                @click="selectedPeriod = p.key"
                class="px-3.5 py-1.5 rounded-full text-xs font-medium transition border"
                :class="selectedPeriod === p.key
                  ? 'text-white border-transparent'
                  : 'text-slate-400 border-slate-700 hover:text-white hover:border-slate-500'"
                :style="selectedPeriod === p.key ? { background: 'linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%)' } : {}"
              >
                {{ p.label }}
              </button>
            </div>
          </div>

          <!-- Transactions -->
          <div class="bg-[#1A1F2B] rounded-[20px] p-6">
            <h2 class="text-lg font-semibold text-white mb-6">Последние транзакции</h2>
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="border-b border-white/5">
                    <th class="pb-3 text-sm font-medium text-slate-400 pr-4">Дата</th>
                    <th class="pb-3 text-sm font-medium text-slate-400 pr-4">Трек</th>
                    <th class="pb-3 text-sm font-medium text-slate-400 pr-4">Площадка</th>
                    <th class="pb-3 text-sm font-medium text-slate-400 text-right">Сумма</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(tx, i) in transactions" :key="i" class="border-b border-white/5 last:border-0">
                    <td class="py-4 text-sm text-white pr-4">{{ tx.date }}</td>
                    <td class="py-4 text-sm text-white pr-4">{{ tx.track }}</td>
                    <td class="py-4 text-sm text-white pr-4">{{ tx.platform }}</td>
                    <td class="py-4 text-sm font-bold text-white text-right">{{ tx.amount.toLocaleString('ru-RU') }} ₽</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- TAB: Tracks -->
        <div v-if="activeTab === 'tracks'" class="bg-[#1A1F2B] rounded-[20px] p-6">
          <h2 class="text-lg font-semibold text-white mb-6">Треки артиста</h2>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="border-b border-white/5">
                  <th class="pb-3 text-sm font-medium text-slate-400 pr-4">Название</th>
                  <th class="pb-3 text-sm font-medium text-slate-400 pr-4">Альбом</th>
                  <th class="pb-3 text-sm font-medium text-slate-400 pr-4">Дата релиза</th>
                  <th class="pb-3 text-sm font-medium text-slate-400 pr-4">Площадка</th>
                  <th class="pb-3 text-sm font-medium text-slate-400 text-right">Доход</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(tr, i) in tracks" :key="i" class="border-b border-white/5 last:border-0">
                  <td class="py-4 text-sm text-white pr-4">{{ tr.title }}</td>
                  <td class="py-4 text-sm text-slate-400 pr-4">{{ tr.album }}</td>
                  <td class="py-4 text-sm text-slate-400 pr-4">{{ tr.date }}</td>
                  <td class="py-4 text-sm text-slate-400 pr-4">{{ tr.platform }}</td>
                  <td class="py-4 text-sm font-bold text-white text-right">{{ tr.revenue.toLocaleString('ru-RU') }} ₽</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- TAB: Finances -->
        <div v-if="activeTab === 'finances'" class="bg-[#1A1F2B] rounded-[20px] p-6">
          <h2 class="text-lg font-semibold text-white mb-6">Доходы по площадкам</h2>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="border-b border-white/5">
                  <th class="pb-3 text-sm font-medium text-slate-400 pr-4">Период</th>
                  <th class="pb-3 text-sm font-medium text-slate-400 pr-4">Площадка</th>
                  <th class="pb-3 text-sm font-medium text-slate-400 pr-4">Сумма</th>
                  <th class="pb-3 text-sm font-medium text-slate-400 text-right">Статус</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(inc, i) in incomes" :key="i" class="border-b border-white/5 last:border-0">
                  <td class="py-4 text-sm text-white pr-4">{{ inc.period }}</td>
                  <td class="py-4 text-sm text-white pr-4">{{ inc.platform }}</td>
                  <td class="py-4 text-sm font-bold text-white pr-4">{{ inc.amount.toLocaleString('ru-RU') }} ₽</td>
                  <td class="py-4 text-right">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400">
                      {{ inc.status }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </LabelLayout>
</template>