<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import StatCard from '@/Components/StatCard.vue'
import LineChart from '@/Components/Charts/LineChart.vue'
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue'
import TrackCard from '@/Components/TrackCard.vue'

const page = usePage()

const user = computed(() => page.props.auth?.user ?? { name: 'Мария Светлова' })

const today = new Intl.DateTimeFormat('ru-RU', {
  day: 'numeric',
  month: 'long',
  year: 'numeric'
}).format(new Date())

const stats = computed(() => page.props.stats ?? {
  balance: '87 500 ₽',
  total_income: '524 300 ₽',
  tracks_count: '1',
  tracks_sub: '+2 за месяц',
  paid_out: '436 800 ₽'
})

// Данные линейного графика
const revenueData = {
  labels: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
  datasets: [{
    label: 'Доход',
    data: [180000, 205000, 220000, 235000, 284000, 350000, 372000, 395000, 430000, 455000, 480000, 520000],
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
  }]
}

// Данные donut-chart
const tracksRevenueData = {
  labels: ['Летняя Ночь', 'Остальные'],
  datasets: [{
    data: [49800,200],
    backgroundColor: ['#7C3AED', '#243041'],
    borderColor: 'white',
    borderWidth: 2,
    hoverOffset: 1
  }]
}

// Мок-данные треков
const tracks = [
  {
    title: 'Летняя Ночь',
    cover: 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=400&h=400&fit=crop',
    share: 40,
    earnings: '49 800'
    
  }
]
</script>

<template>
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
          <div 
            class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-xl border-2"
            style="border-color: #7C3AED;"
          >
            <span 
              class="flex h-full w-full items-center justify-center text-lg font-bold text-white"
              style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
            >
              {{ user.name.charAt(0) }}
            </span>
          </div>
        </div>

        <!-- KPI Grid -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
          
          <StatCard 
            title="Мой баланс" 
            :value="stats.balance" 
            subtitle="Доступно для вывода" 
            iconBg="bg-emerald-500"
          >
            <template #icon>
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
              </svg>
            </template>
          </StatCard>

          <StatCard 
            title="Общий доход" 
            :value="stats.total_income" 
            subtitle="За всё время" 
            iconBg="bg-fuchsia-600"
          >
            <template #icon>
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                <path d="M12 2v20" />
              </svg>
            </template>
          </StatCard>

          <StatCard 
            title="Мои треки" 
            :value="stats.tracks_count" 
            :subtitle="stats.tracks_sub" 
            iconBg="bg-blue-500"
          >
            <template #icon>
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z" />
              </svg>
            </template>
          </StatCard>

          <StatCard 
            title="Выплачено" 
            :value="stats.paid_out" 
            subtitle="Всего получено" 
            iconBg="bg-orange-500"
          >
            <template #icon>
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
              </svg>
            </template>
          </StatCard>
        </div>

        <!-- Charts -->
        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
          
          <!-- Линейный график -->
          <div 
            class="rounded-[12px] p-6 lg:col-span-2"
            style="background-color: #1A1F2B; box-shadow: 0px 4px 6px rgba(0,0,0,0.3);"
          >
            <h2 class="mb-6 text-lg font-semibold" style="color: #F8FAFC;">Мои доходы по месяцам</h2>
            <div class="h-[320px] w-full">
              <LineChart :chart-data="revenueData" />
            </div>
          </div>

          <!-- Donut -->
          <div 
            class="rounded-[12px] p-6"
            style="background-color: #1A1F2B; box-shadow: 0px 4px 6px rgba(0,0,0,0.3);"
          >
            <h2 class="mb-6 text-lg font-semibold" style="color: #F8FAFC;">Доход по трекам</h2>
            <div class="h-[220px] w-full">
              <DoughnutChart :chart-data="tracksRevenueData" />
            </div>
            <div 
              class="mt-6 flex items-center justify-between rounded-lg px-3 py-2"
              style="background-color: #0B0E14;"
            >
              <div class="flex items-center gap-2">
                <span class="h-3 w-3 rounded-full" style="background-color: #7C3AED;"></span>
                <span class="text-sm" style="color: #94A3B8;">Летняя Ночь</span>
              </div>
              <span class="text-sm font-semibold text-white">50K</span>
            </div>
          </div>
        </div>

        <!-- Мои треки -->
        <div class="mt-10">
          <h2 class="mb-6 text-xl font-semibold" style="color: #F8FAFC;">Мои треки</h2>
          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <TrackCard class="border-2 border-[#7C3AED]" v-for="track in tracks" :key="track.title" :track="track" />
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>