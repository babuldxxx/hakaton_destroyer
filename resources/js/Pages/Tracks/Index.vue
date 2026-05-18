<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const page = usePage()

// Определяем лейбл ли пользователь (работает и со строкой, и с Enum)
const isLabel = computed(() => {
    const r = page.props.auth?.user?.role
    if (typeof r === 'string') return r === 'label'
    if (r && typeof r === 'object') return r.value === 'label'
    return false
})

const props = defineProps({
    tracks: [Object, Array] // может быть пагинатором или массивом
})

// Работаем и с пагинацией (tracks.data), и с обычным массивом
const trackList = computed(() => props.tracks?.data ?? props.tracks ?? [])

const deleteTrack = (id) => {
    if (!confirm('Точно удалить трек?')) return
    router.delete(route('tracks.destroy', id), {
        preserveScroll: true,
    })
}
</script>

<template>
  <Head :title="isLabel ? 'Треки' : 'Мои треки'" />

  <AuthenticatedLayout>
    <div class="p-6 md:p-10 max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-white">
          {{ isLabel ? 'Треки' : 'Мои треки' }}
        </h1>

        <!-- Кнопка добавления — только лейбл -->
        <Link
          v-if="isLabel"
          :href="route('tracks.create')"
          class="px-4 py-2 rounded-lg text-sm font-medium text-white transition hover:opacity-90"
          style="background: linear-gradient(135deg, #7C3AED 0%, #3B82F6 100%);"
        >
          + Добавить трек
        </Link>
      </div>

      <!-- Search (можно оставить для всех) -->
      <div class="mb-6 relative">
        <input
          type="text"
          placeholder="Поиск треков..."
          class="w-full rounded-lg border bg-[#161922] px-4 py-3 pl-10 text-sm text-white placeholder-gray-500 focus:border-indigo-500 focus:outline-none"
          style="border-color: #2D3748;"
        />
        <span class="absolute left-3 top-3.5 text-gray-500">🔍</span>
      </div>

      <!-- Empty state -->
      <div v-if="trackList.length === 0" class="text-center py-20 text-gray-500">
        Нет добавленных треков.
      </div>

      <!-- Grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <div
          v-for="track in trackList"
          :key="track.id"
          class="rounded-xl border p-4 transition hover:border-indigo-500/50"
          style="background-color: #1A1F2B; border-color: #2D3748;"
        >
          <!-- Cover -->
          <div class="w-full aspect-square rounded-lg bg-gray-800 mb-3 overflow-hidden">
            <img
              v-if="track.cover_url"
              :src="track.cover_url"
              class="w-full h-full object-cover"
              :alt="track.title"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-gray-600 text-4xl">♪</div>
          </div>

          <!-- Info -->
          <h3 class="font-semibold text-white truncate">{{ track.title }}</h3>
          <p class="text-sm text-gray-400 mt-1 truncate">
            {{ track.artists?.map(a => a.stage_name ?? a.real_name).join(', ') ?? 'Без артиста' }}
          </p>
          <p class="text-xs text-gray-500 mt-0.5">{{ track.genre?.name ?? 'Без жанра' }}</p>

          <!-- Actions -->
          <div class="flex items-center gap-2 mt-4 flex-wrap">
            <!-- Label: Изменить + Удалить -->
            <template v-if="isLabel">
              <Link
                :href="route('tracks.edit', track.id)"
                class="text-xs px-3 py-1.5 rounded-md bg-indigo-600 text-white hover:bg-indigo-500 transition"
              >
                Изменить
              </Link>
              <button
                @click="deleteTrack(track.id)"
                class="text-xs px-3 py-1.5 rounded-md bg-red-600/80 text-white hover:bg-red-500 transition"
              >
                Удалить
              </button>
            </template>

            <!-- Artist: только смотреть -->
            <template v-else>
              <Link
                :href="route('tracks.show', track.id)"
                class="text-xs px-3 py-1.5 rounded-md bg-gray-700 text-gray-200 hover:bg-gray-600 transition"
              >
                Подробнее
              </Link>
            </template>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="tracks?.links" class="mt-8 flex justify-center">
        <div class="flex gap-2">
          <Link
            v-for="(link, i) in tracks.links"
            :key="i"
            :href="link.url"
            v-html="link.label"
            class="px-3 py-1 rounded text-sm transition"
            :class="link.active
              ? 'bg-indigo-600 text-white'
              : 'bg-[#1A1F2B] text-gray-400 hover:text-white'"
            style="border: 1px solid #2D3748;"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>