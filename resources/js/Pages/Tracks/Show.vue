<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  song: {
    type: Object,
    required: true
  }
})

const earningForm = useForm({
  platform_id: props.song.platforms?.[0]?.id ?? null,
  amount: '',
  period: new Date().toISOString().slice(0, 7),
})

function addEarning() {
  earningForm.post(route('tracks.earnings.store', props.song.id), {
    preserveScroll: true,
    onSuccess: () => earningForm.reset('amount'),
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6 md:p-10 max-w-5xl mx-auto text-white">

      <!-- Шапка трека -->
      <div class="flex flex-col md:flex-row gap-6 mb-8">
        <div class="w-full md:w-64 shrink-0">
          <div class="aspect-square rounded-xl overflow-hidden bg-gray-800 border border-gray-700">
            <img :src="song.cover_url" class="w-full h-full object-cover" :alt="song.title" />
          </div>
        </div>

        <div class="flex-1">
          <h1 class="text-3xl font-bold mb-2">{{ song.title }}</h1>

          <p class="text-gray-400 mb-1">
            Жанр: {{ song.genre }}
            <span v-if="song.release_date && song.release_date !== '—'">• Релиз: {{ song.release_date }}</span>
          </p>
          <p v-if="song.written_at" class="text-gray-400 mb-3">Написана: {{ song.written_at }}</p>

          <!-- Площадки -->
          <div v-if="song.platforms?.length" class="flex flex-wrap gap-2 mb-4">
            <span
              v-for="p in song.platforms"
              :key="p.id"
              class="inline-flex items-center rounded px-2 py-1 text-xs font-medium uppercase tracking-wide bg-gray-800 border border-gray-700 text-gray-300"
            >
              {{ p.name }}
            </span>
          </div>
          <div v-else class="mb-4 text-xs text-gray-600">
            Площадки не выбраны
          </div>

          <!-- Плеер -->
          <div v-if="song.mp3_url" class="mt-2">
            <audio controls class="w-full">
              <source :src="song.mp3_url" type="audio/mpeg" />
            </audio>
          </div>
        </div>
      </div>

      <!-- Текст -->
      <div
        v-if="song.lyrics"
        class="mb-8 rounded-xl border p-5"
        style="background-color: #1A1F2B; border-color: #2D3748;"
      >
        <h3 class="text-lg font-semibold mb-3">Текст песни</h3>
        <pre class="whitespace-pre-wrap text-sm text-gray-300 font-sans">{{ song.lyrics }}</pre>
      </div>

      <!-- Блок доходов -->
      <div
        class="rounded-xl border p-5"
        style="background-color: #1A1F2B; border-color: #2D3748;"
      >
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Доходы по площадкам</h3>
          <span v-if="song.total_revenue !== '0.00'" class="text-sm text-gray-400">
            Всего: <strong class="text-white">{{ song.total_revenue }} ₽</strong>
          </span>
        </div>

        <!-- Список начислений -->
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
        <div v-else class="text-sm text-gray-500 mb-6">
          Пока нет начислений.
        </div>

        <!-- Форма добавления -->
        <form
          v-if="song.platforms?.length"
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
              <option v-for="p in song.platforms" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>
            <div v-if="earningForm.errors.platform_id" class="text-red-400 text-xs mt-1">
              {{ earningForm.errors.platform_id }}
            </div>
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
            <div v-if="earningForm.errors.amount" class="text-red-400 text-xs mt-1">
              {{ earningForm.errors.amount }}
            </div>
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

        <div v-else class="text-xs text-gray-600">
          Сначала прикрепите площадки к треку через «Редактировать».
        </div>
      </div>

      <!-- Назад -->
      <div class="mt-8">
        <Link :href="route('tracks.index')" class="text-sm text-gray-400 hover:text-white transition">
          ← Назад к трекам
        </Link>
      </div>

    </div>
  </AuthenticatedLayout>
</template>