<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TrackItemCard from '@/Components/TrackItemCard.vue'

// Получаем реальные данные от SongController
const props = defineProps({
  songs: Object
})

const search = ref('')

// Превращаем данные бэкенда в формат, который ждёт TrackItemCard
const tracks = computed(() => {
  if (!props.songs?.data) return []

  return props.songs.data.map(song => ({
    id: song.id,
    title: song.title,
    cover: song.cover_url || 'https://unsplash.com',
    genre: song.genre?.name || 'Без жанра',
    release_date: song.released_at
      ? new Date(song.released_at).toLocaleDateString('ru-RU')
      : '—',
    share: song.song_authors?.[0]?.share_percentage ?? 0,
    platforms: ['VK Music', 'Apple Music', 'Яндекс Музыка'], // пока заглушка
    total_revenue: '0', // пока заглушка, пока нет начислений
    earnings: '0'
  }))
})

const filteredTracks = computed(() => {
  if (!search.value) return tracks.value
  return tracks.value.filter(t =>
    t.title.toLowerCase().includes(search.value.toLowerCase()) ||
    t.genre.toLowerCase().includes(search.value.toLowerCase())
  )
})
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6 md:p-10">
      <div class="mx-auto max-w-6xl">

        <div class="mb-8 flex items-center justify-between">
          <h1 class="text-[32px] font-bold" style="color: #F8FAFC;">Мои треки</h1>
          <Link 
            href="/tracks/create" 
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition"
          >
            + Добавить трек
          </Link>
        </div>

        <div class="mb-8">
          <div
            class="flex items-center gap-3 rounded-xl border px-4 py-3"
            style="background-color: #1A1F2B; border-color: #2D3748;"
          >
            <svg class="h-5 w-5 flex-shrink-0" style="color: #64748B;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input
              v-model="search"
              type="text"
              placeholder="Поиск треков..."
              class="w-full bg-transparent text-sm text-white placeholder-gray-500 outline-none"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <Link
            v-for="track in filteredTracks"
            :key="track.id"
            :href="`/tracks/${track.id}`"
            class="block transition hover:opacity-90"
          >
            <TrackItemCard :track="track" />
          </Link>
        </div>

        <div v-if="filteredTracks.length === 0" class="py-20 text-center">
          <p class="text-lg" style="color: #64748B;">
            {{ props.songs?.data?.length ? 'Треки не найдены' : 'Нет добавленных треков' }}
          </p>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>