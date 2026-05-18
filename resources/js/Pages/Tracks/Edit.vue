<script setup>
import { useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  song:      { type: Object, required: true },
  genres:    { type: Array, default: () => [] },
  platforms: { type: Array, default: () => [] },
})

const form = useForm({
  title: props.song.title,
  lyrics: props.song.lyrics,
  written_at: props.song.written_at,
  released_at: props.song.released_at,
  genre_id: props.song.genre_id,
  platforms: props.song.platform_ids || [],
})

function submit() {
  form.put(route('tracks.update', props.song.id))
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6 md:p-10 max-w-3xl mx-auto text-white">
      <h1 class="text-2xl font-bold mb-6">Редактировать трек</h1>

      <form @submit.prevent="submit" class="space-y-6">

        <div>
          <label class="block mb-2 text-sm text-gray-400">Название</label>
          <input v-model="form.title" type="text"
            class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white outline-none focus:border-indigo-500" />
          <div v-if="form.errors.title" class="text-red-400 text-sm mt-1">{{ form.errors.title }}</div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block mb-2 text-sm text-gray-400">Дата написания</label>
            <input v-model="form.written_at" type="date"
              class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white outline-none focus:border-indigo-500" />
          </div>
          <div>
            <label class="block mb-2 text-sm text-gray-400">Дата релиза</label>
            <input v-model="form.released_at" type="date"
              class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white outline-none focus:border-indigo-500" />
          </div>
        </div>

        <div>
          <label class="block mb-2 text-sm text-gray-400">Жанр</label>
          <select v-model="form.genre_id"
            class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white outline-none focus:border-indigo-500">
            <option :value="null">Без жанра</option>
            <option v-for="g in genres" :key="g.id" :value="g.id">{{ g.name }}</option>
          </select>
        </div>

        <div>
          <label class="block mb-2 text-sm text-gray-400">Текст песни</label>
          <textarea v-model="form.lyrics" rows="4"
            class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white outline-none focus:border-indigo-500"></textarea>
        </div>

        <div>
          <label class="block mb-2 text-sm text-gray-400">Стриминговые площадки</label>
          <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <label v-for="p in platforms" :key="p.id"
              class="flex items-center gap-3 p-3 bg-gray-800 border border-gray-700 rounded-lg cursor-pointer hover:border-indigo-500 transition select-none">
              <input v-model="form.platforms" type="checkbox" :value="p.id"
                class="w-4 h-4 text-indigo-600 bg-gray-900 border-gray-700 rounded focus:ring-indigo-500 focus:ring-offset-gray-800" />
              <span class="text-sm font-medium text-gray-200">{{ p.name }}</span>
            </label>
          </div>
        </div>

        <div class="pt-4">
          <button type="submit" :disabled="form.processing"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition disabled:opacity-50">
            {{ form.processing ? 'Сохраняем...' : 'Сохранить изменения' }}
          </button>
        </div>

      </form>
    </div>
  </AuthenticatedLayout>
</template>