<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const isLabel = computed(() => page.props.auth.user?.role === 'label')

const props = defineProps({
  song: { type: Object, required: true },
  platforms: { type: Array, default: () => [] },
})

// Начисления
const earningForm = useForm({
  platform_id: props.platforms[0]?.id ?? null,
  amount: '',
  period: new Date().toISOString().slice(0, 7),
})

function addEarning() {
  earningForm.post(route('tracks.earnings.store', props.song.id), {
    preserveScroll: true,
    onSuccess: () => earningForm.reset('amount'),
  })
}

const roleLabels = {
  author: 'Автор',
  performer: 'Исполнитель',
  producer: 'Продюсер',
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6 md:p-10 max-w-5xl mx-auto text-white">

      <!-- Шапка -->
      <div class="flex flex-col md:flex-row gap-6 mb-8">
        <div class="w-full md:w-64 shrink-0">
          <div class="aspect-square rounded-xl overflow-hidden bg-gray-800 border border-gray-700 flex items-center justify-center">
            <img
              v-if="song.cover_url"
              :src="song.cover_url"
              class="w-full h-full object-cover"
              :alt="song.title"
            />
            <span v-else class="text-gray-500 text-sm">Нет обложки</span>
          </div>
        </div>

        <div class="flex-1">
          <div class="flex items-start justify-between gap-4 mb-2">
            <h1 class="text-3xl font-bold">{{ song.title }}</h1>
            <Link
              v-if="isLabel"
              :href="route('tracks.edit', song.id)"
              class="shrink-0 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition"
            >
              Редактировать
            </Link>
          </div>

          <p class="text-gray-400 mb-1">
            Жанр: {{ song.genre }}
            <span v-if="song.release_date && song.release_date !== '—'">• Релиз: {{ song.release_date }}</span>
          </p>
          <p v-if="song.written_at" class="text-gray-400 mb-3">Написана: {{ song.written_at }}</p>

          <!-- РАБОТАЮЩИЙ ПЛЕЕР -->
          <div v-if="song.mp3_url" class="mt-2">
            <audio controls class="w-full">
              <source :src="song.mp3_url" type="audio/mpeg" />
              Ваш браузер не поддерживает аудио.
            </audio>
          </div>
          <div v-else class="mt-2 text-sm text-gray-600">Аудиофайл не загружен</div>

          <div v-if="song.wav_url" class="mt-3">
            <a
              :href="song.wav_url"
              download
              class="inline-flex items-center gap-2 text-indigo-400 hover:text-indigo-300 text-sm font-medium"
            >
              ⬇️ Скачать WAV
            </a>
          </div>
        </div>
      </div>

      <!-- Текст песни -->
      <div v-if="song.lyrics" class="mb-6 rounded-xl border p-5" style="background-color: #1A1F2B; border-color: #2D3748;">
        <h3 class="text-lg font-semibold mb-3">Текст песни</h3>
        <pre class="whitespace-pre-wrap text-sm text-gray-300 font-sans">{{ song.lyrics }}</pre>
      </div>

      <!-- АВТОРЫ И ДОЛИ -->
      <div class="mb-6 rounded-xl border p-5" style="background-color: #1A1F2B; border-color: #2D3748;">
        <h3 class="text-lg font-semibold mb-4">Авторы и доли</h3>

        <div v-if="song.song_authors?.length" class="space-y-2">
          <div
            v-for="author in song.song_authors"
            :key="author.id"
            class="flex items-center justify-between py-2.5 border-b border-gray-700/30 last:border-0"
          >
            <div class="flex items-center gap-3">
              <span class="text-white font-medium">
                {{ author.artist?.stage_name || author.artist?.real_name || 'Неизвестный артист' }}
              </span>
              <span class="text-xs px-2 py-0.5 rounded-full bg-gray-700 text-gray-300 border border-gray-600">
                {{ roleLabels[author.role] || author.role }}
              </span>
            </div>
            <span class="text-gray-400 text-sm font-medium">{{ author.share_percentage }}%</span>
          </div>
        </div>
        <p v-else class="text-gray-500 text-sm">Авторы не указаны.</p>
      </div>

      <!-- Доходы по площадкам -->
      <div class="rounded-xl border p-5" style="background-color: #1A1F2B; border-color: #2D3748;">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Доходы по площадкам</h3>
          <span v-if="song.total_revenue !== '0.00'" class="text-sm text-gray-400">
            Всего: <strong class="text-white">{{ song.total_revenue }} ₽</strong>
          </span>
        </div>

        <div v-if="song.earnings_list?.length" class="space-y-2 mb-6">
          <div
            v-for="e in song.earnings_list"
            :key="e.id"
            class="flex items-center justify-between p-3 rounded bg-gray-800/50"
          >
            <span class="text-sm text-gray-300">{{ e.platform }} — {{ e.period }}</span>
            <span class="text-sm font-medium text-white">{{ e.amount }} ₽</span>
          </div>
          <div class="flex justify-between pt-3 border-t border-gray-700 text-white font-semibold">
            <span>Итого</span>
            <span>{{ song.total_revenue }} ₽</span>
          </div>
        </div>
        <div v-else class="text-sm text-gray-500 mb-6">Пока нет начислений.</div>

        <form
          v-if="platforms.length && isLabel"
          @submit.prevent="addEarning"
          class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end"
        >
          <div class="md:col-span-2">
            <label class="block text-xs text-gray-400 mb-1">Площадка</label>
            <select
              v-model="earningForm.platform_id"
              required
              class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm focus:border-indigo-500 outline-none"
            >
              <option v-for="p in platforms" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-400 mb-1">Сумма (₽)</label>
            <input
              v-model.number="earningForm.amount"
              type="number"
              step="0.01"
              min="0"
              required
              class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm focus:border-indigo-500 outline-none"
            />
          </div>
          <div>
            <label class="block text-xs text-gray-400 mb-1">Период</label>
            <input
              v-model="earningForm.period"
              type="month"
              required
              class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm focus:border-indigo-500 outline-none"
            />
          </div>
          <div class="md:col-span-4">
            <button
              type="submit"
              :disabled="earningForm.processing"
              class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition disabled:opacity-50"
            >
              {{ earningForm.processing ? 'Сохраняем...' : '+ Добавить начисление' }}
            </button>
          </div>
        </form>
      </div>

      <div class="mt-8">
        <Link :href="route('tracks.index')" class="text-sm text-gray-400 hover:text-white transition">
          ← Назад к трекам
        </Link>
      </div>
    </div>
  </AuthenticatedLayout>
</template>