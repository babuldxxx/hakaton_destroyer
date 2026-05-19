<script setup>
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import LabelLayout from '@/Layouts/LabelLayout.vue'

const genres = ['Все', 'Поп', 'Хип-хоп', 'Электронная', 'Рок', 'Джаз']
const selectedGenre = ref('Все')
const searchQuery = ref('')

const artists = ref([
  {
    id: 1,
    name: 'Мария Светлова',
    genre: 'Поп',
    avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=300&h=300&fit=crop&crop=face',
    tracks: 12,
    revenue: 524300,
    gradFrom: '#7C3AED',
    gradTo: '#3B82F6',
  },
  {
    id: 2,
    name: 'Тёмный Бит',
    genre: 'Хип-хоп',
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=300&fit=crop&crop=face',
    tracks: 24,
    revenue: 833000,
    gradFrom: '#EC4899',
    gradTo: '#BE185D',
  },
  {
    id: 3,
    name: 'Эхо Ночи',
    genre: 'Электронная',
    avatar: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=300&h=300&fit=crop&crop=face',
    tracks: 8,
    revenue: 312500,
    gradFrom: '#10B981',
    gradTo: '#059669',
  },
  {
    id: 4,
    name: 'Рок Волна',
    genre: 'Рок',
    avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=300&h=300&fit=crop&crop=face',
    tracks: 15,
    revenue: 445000,
    gradFrom: '#F59E0B',
    gradTo: '#D97706',
  },
])

const filteredArtists = computed(() =>
  artists.value.filter(a => {
    const byGenre = selectedGenre.value === 'Все' || a.genre === selectedGenre.value
    const bySearch = a.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    return byGenre && bySearch
  })
)
</script>

<template>
  <Head title="Мои артисты" />
  <LabelLayout>
    <div class="p-6 md:p-10">
      <div class="mx-auto max-w-6xl">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
          <h1 class="text-[32px] font-bold text-white">Мои артисты</h1>
          <button class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-medium transition hover:opacity-90"
            style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Добавить артиста
          </button>
        </div>

        <!-- Filters -->
        <div class="flex flex-col lg:flex-row gap-4 mb-8">
          <!-- Search -->
          <div class="relative lg:w-72">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
              <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Поиск..."
              class="block w-full rounded-xl border-0 bg-[#1A1F2B] py-2.5 pl-10 pr-4 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-violet-500/30 outline-none transition"
            />
          </div>

          <!-- Genre chips -->
          <div class="flex flex-wrap gap-2">
            <button
              v-for="g in genres"
              :key="g"
              @click="selectedGenre = g"
              class="px-4 py-2 rounded-xl text-sm font-medium transition border"
              :class="selectedGenre === g ? 'text-white border-transparent' : 'text-slate-400 border-slate-700 hover:text-white hover:border-slate-500'"
              :style="selectedGenre === g ? { background: 'linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%)' } : { backgroundColor: '#0B0E14' }"
            >
              {{ g }}
            </button>
          </div>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
          <Link
            v-for="a in filteredArtists"
            :key="a.id"
            :href="`/label/artists/${a.id}`"
            class="group relative bg-[#1A1F2B] rounded-[20px] p-6 transition-all duration-300 hover:shadow-[0_0_0_1.5px_rgba(124,58,237,0.5)]"
          >
            <!-- Avatar -->
            <div class="flex justify-center mb-4">
              <div class="w-28 h-28 rounded-2xl p-1" :style="{ background: `linear-gradient(135deg, ${a.gradFrom} 0%, ${a.gradTo} 100%)` }">
                <img :src="a.avatar" :alt="a.name" class="w-full h-full object-cover rounded-xl bg-[#0B0E14]" />
              </div>
            </div>

            <!-- Name & Genre -->
            <div class="text-center">
              <h3 class="text-lg font-bold text-white mb-2">{{ a.name }}</h3>
              <span class="inline-block px-3 py-1 rounded-full text-xs font-medium mb-4"
                :class="{
                  'bg-violet-500/10 text-violet-400': a.genre === 'Поп',
                  'bg-pink-500/10 text-pink-400': a.genre === 'Хип-хоп',
                  'bg-emerald-500/10 text-emerald-400': a.genre === 'Электронная',
                  'bg-orange-500/10 text-orange-400': a.genre === 'Рок',
                  'bg-blue-500/10 text-blue-400': a.genre === 'Джаз',
                }">
                {{ a.genre }}
              </span>
            </div>

            <!-- Stats: треки / доход -->
            <div class="grid grid-cols-2 divide-x divide-white/5 border-t border-white/5 pt-4 mb-4">
              <div class="flex flex-col items-center gap-1">
                <div class="flex items-center gap-1.5 text-xs text-slate-400">
                  <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z" />
                  </svg>
                  Треки
                </div>
                <div class="text-xl font-bold text-white">{{ a.tracks }}</div>
              </div>
              <div class="flex flex-col items-center gap-1">
                <div class="flex items-center gap-1.5 text-xs text-slate-400">
                  <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Доход
                </div>
                <div class="text-xl font-bold text-white">{{ Math.round(a.revenue/1000) }}K</div>
              </div>
            </div>

            <!-- Hover button -->
            <div class="opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300">
              <div class="w-full py-2.5 rounded-xl text-center text-white text-sm font-medium" style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);">
                Подробнее
              </div>
            </div>
          </Link>
        </div>

      </div>
    </div>
  </LabelLayout>
</template>