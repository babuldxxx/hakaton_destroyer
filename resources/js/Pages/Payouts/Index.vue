<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import LabelLayout from '@/Layouts/LabelLayout.vue'

/* ─── Фильтр ─── */
const activeFilter = ref('all')
const filters = [
  { key: 'all', label: 'Все' },
  { key: 'pending', label: 'В ожидании' },
  { key: 'completed', label: 'Завершено' },
]

/* ─── Демо-данные (как на скриншоте) ─── */
const payouts = [
  { id: 1, date: '2025-11-15', artist: 'Тёмный Бит', comment: 'Выплата за октябрь 2025', amount: 250000, status: 'completed' },
  { id: 2, date: '2025-11-15', artist: 'Мария Светлова', comment: 'Выплата за октябрь 2025', amount: 180000, status: 'completed' },
  { id: 3, date: '2025-12-01', artist: 'Эхо Ночи', comment: 'Ожидает обработки', amount: 220000, status: 'pending' },
  { id: 4, date: '2025-12-01', artist: 'Рок Волна', comment: 'Ожидает обработки', amount: 145000, status: 'pending' },
]

const filtered = computed(() => {
  if (activeFilter.value === 'all') return payouts
  return payouts.filter(p => p.status === activeFilter.value)
})

/* ─── Статистика ─── */
const stats = computed(() => {
  const pending = payouts.filter(p => p.status === 'pending').reduce((s, p) => s + p.amount, 0)
  const completed = payouts.filter(p => p.status === 'completed').reduce((s, p) => s + p.amount, 0)
  return { pending, completed, total: payouts.length }
})

const fmt = (n) => n.toLocaleString('ru-RU') + ' ₽'
</script>

<template>
  <Head title="Выплаты" />
  <LabelLayout>
    <div class="p-6 md:p-10">
      <div class="mx-auto max-w-5xl">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
          <h1 class="text-3xl md:text-4xl font-bold text-white">Выплаты</h1>
          <button
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium text-white transition shadow-lg shadow-violet-900/20 hover:opacity-90 hover:scale-[1.02] active:scale-[0.98]"
            style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%)"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Инициировать выплату
          </button>
        </div>

        <!-- Stats cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6 mb-8">
          <!-- Ожидают -->
          <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5">
            <div class="w-12 h-12 rounded-xl bg-amber-500/15 flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="text-sm text-slate-400 mb-1">Ожидают выплаты</div>
            <div class="text-2xl font-bold text-white">{{ fmt(stats.pending) }}</div>
          </div>
          <!-- Выплачено -->
          <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/15 flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="text-sm text-slate-400 mb-1">Выплачено</div>
            <div class="text-2xl font-bold text-white">{{ fmt(stats.completed) }}</div>
          </div>
          <!-- Всего -->
          <div class="bg-[#1A1F2B] rounded-2xl p-6 border border-white/5">
            <div class="w-12 h-12 rounded-xl bg-violet-500/15 flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="text-sm text-slate-400 mb-1">Всего транзакций</div>
            <div class="text-2xl font-bold text-white">{{ stats.total }}</div>
          </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap gap-2 mb-6">
          <button
            v-for="f in filters"
            :key="f.key"
            @click="activeFilter = f.key"
            class="px-5 py-2 rounded-full text-sm font-medium border transition select-none"
            :class="activeFilter === f.key
              ? 'text-white border-transparent'
              : 'bg-[#1A1F2B] text-slate-400 border-slate-700 hover:text-white hover:border-slate-500'"
            :style="activeFilter === f.key ? { background: 'linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%)' } : {}"
          >
            {{ f.label }}
          </button>
        </div>

        <!-- Table -->
        <div class="bg-[#1A1F2B] rounded-2xl border border-white/5">
          <div class="p-6 border-b border-white/5">
            <h2 class="text-lg font-semibold text-white">История выплат</h2>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="border-b border-white/5 text-slate-400 text-sm">
                  <th class="py-4 px-6 font-medium whitespace-nowrap">Дата</th>
                  <th class="py-4 px-6 font-medium whitespace-nowrap">Артист</th>
                  <th class="py-4 px-6 font-medium whitespace-nowrap">Комментарий</th>
                  <th class="py-4 px-6 font-medium whitespace-nowrap text-right">Сумма</th>
                  <th class="py-4 px-6 font-medium whitespace-nowrap text-right">Статус</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="p in filtered"
                  :key="p.id"
                  class="border-b border-white/5 last:border-0 transition-colors hover:bg-white/[0.03]"
                >
                  <td class="py-4 px-6 text-sm text-slate-300 whitespace-nowrap">{{ p.date }}</td>
                  <td class="py-4 px-6 text-sm text-white whitespace-nowrap">{{ p.artist }}</td>
                  <td class="py-4 px-6 text-sm text-slate-400 whitespace-nowrap">{{ p.comment }}</td>
                  <td class="py-4 px-6 text-sm font-bold text-white whitespace-nowrap text-right">{{ fmt(p.amount) }}</td>
                  <td class="py-4 px-6 text-right whitespace-nowrap">
                    <span
                      v-if="p.status === 'completed'"
                      class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                      Выплачено
                    </span>
                    <span
                      v-else
                      class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      В ожидании
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