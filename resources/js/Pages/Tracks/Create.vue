<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  genres: Array,
  artists: Array,
})

const form = useForm({
  title: '',
  lyrics: '',
  written_at: '',
  released_at: '',
  genre_id: '',
  cover: null,
  mp3: null,
  wav: null,
  authors: [],
})

function addAuthor() {
  form.authors.push({ artist_id: '', share_percentage: '', role: 'author' })
}

function removeAuthor(index) {
  form.authors.splice(index, 1)
}

function submit() {
  form.post(route('tracks.store'), {
    forceFormData: true,
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6 md:p-10 max-w-3xl mx-auto text-white">
      <h1 class="text-2xl font-bold mb-6">Добавить трек</h1>

      <form @submit.prevent="submit" class="space-y-5">
        <div>
          <label class="block text-sm text-gray-400 mb-1">Название</label>
          <input v-model="form.title" type="text" required
            class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm focus:border-indigo-500 outline-none" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-400 mb-1">Жанр</label>
            <select v-model="form.genre_id"
              class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm focus:border-indigo-500 outline-none">
              <option value="">Без жанра</option>
              <option v-for="g in genres" :key="g.id" :value="g.id">{{ g.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm text-gray-400 mb-1">Дата релиза</label>
            <input v-model="form.released_at" type="date"
              class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm focus:border-indigo-500 outline-none" />
          </div>
        </div>

        <div>
          <label class="block text-sm text-gray-400 mb-1">Текст песни</label>
          <textarea v-model="form.lyrics" rows="4"
            class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm focus:border-indigo-500 outline-none"></textarea>
        </div>

        <!-- Авторы -->
        <div>
          <div class="flex items-center justify-between mb-2">
            <label class="block text-sm text-gray-400">Авторы и доли</label>
            <button type="button" @click="addAuthor"
              class="text-sm text-indigo-400 hover:text-indigo-300">+ Добавить автора</button>
          </div>
          <div v-for="(author, index) in form.authors" :key="index"
            class="grid grid-cols-12 gap-2 mb-2">
            <div class="col-span-5">
              <select v-model="author.artist_id" required
                class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm">
                <option value="">Выберите артиста</option>
                <option v-for="a in artists" :key="a.id" :value="a.id">{{ a.stage_name ?? a.real_name }}</option>
              </select>
            </div>
            <div class="col-span-3">
              <input v-model.number="author.share_percentage" type="number" min="0" max="100" required placeholder="%"
                class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm" />
            </div>
            <div class="col-span-3">
              <select v-model="author.role" required
                class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm">
                <option value="author">Автор</option>
                <option value="performer">Исполнитель</option>
                <option value="producer">Продюсер</option>
              </select>
            </div>
            <div class="col-span-1 flex items-center">
              <button type="button" @click="removeAuthor(index)" class="text-red-400 hover:text-red-300">×</button>
            </div>
          </div>
        </div>

        <!-- Файлы -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm text-gray-400 mb-1">Обложка</label>
            <input type="file" accept="image/*" @input="form.cover = $event.target.files[0]"
              class="w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600" />
          </div>
          <div>
            <label class="block text-sm text-gray-400 mb-1">MP3</label>
            <input type="file" accept="audio/mpeg" @input="form.mp3 = $event.target.files[0]"
              class="w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600" />
          </div>
          <div>
            <label class="block text-sm text-gray-400 mb-1">WAV</label>
            <input type="file" accept="audio/wav" @input="form.wav = $event.target.files[0]"
              class="w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600" />
          </div>
        </div>

        <div v-if="form.errors.cover" class="text-red-400 text-sm">{{ form.errors.cover }}</div>
        <div v-if="form.errors.mp3" class="text-red-400 text-sm">{{ form.errors.mp3 }}</div>
        <div v-if="form.errors.wav" class="text-red-400 text-sm">{{ form.errors.wav }}</div>
        <div v-if="form.errors.authors" class="text-red-400 text-sm">{{ form.errors.authors }}</div>

        <div class="flex items-center gap-3 pt-2">
          <button type="submit" :disabled="form.processing"
            class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition disabled:opacity-50">
            Сохранить
          </button>
          <Link :href="route('tracks.index')" class="text-sm text-gray-400 hover:text-white transition">
            Отмена
          </Link>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>