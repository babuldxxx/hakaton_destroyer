<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  song: Object,
  genres: Array,
  artists: Array,
})

const form = useForm({
  title: props.song.title ?? '',
  lyrics: props.song.lyrics ?? '',
  written_at: props.song.written_at ?? '',
  released_at: props.song.released_at ?? '',
  genre_id: props.song.genre_id ?? '',
  cover: null,
  mp3: null,
  wav: null,
  _method: 'put',
  authors: props.song.song_authors?.map(a => ({
    artist_id: a.artist_id,
    share_percentage: a.share_percentage,
    role: a.role,
  })) || [],
  platforms: props.song.platform_ids ?? [],
})

function addAuthor() {
  form.authors.push({
    artist_id: '',
    share_percentage: 0,
    role: 'author',
  })
}

function removeAuthor(index) {
  form.authors.splice(index, 1)
}

function submit() {
  form.post(route('tracks.update', props.song.id), {
    forceFormData: true,
    preserveScroll: true,
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Редактировать трек" />
    <div class="p-6 md:p-10 max-w-3xl mx-auto text-white">
      <h1 class="text-2xl font-bold mb-6">Редактировать трек</h1>

      <form @submit.prevent="submit" class="space-y-5">
        <!-- Название -->
        <div>
          <label class="block text-sm text-gray-400 mb-1">Название</label>
          <input v-model="form.title" type="text" required
            class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm focus:border-indigo-500 outline-none" />
          <div v-if="form.errors.title" class="text-red-400 text-sm mt-1">{{ form.errors.title }}</div>
        </div>

        <!-- Жанр и дата -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-400 mb-1">Жанр</label>
            <select v-model="form.genre_id"
              class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm focus:border-indigo-500 outline-none">
              <option value="">Без жанра</option>
              <option v-for="g in genres" :key="g.id" :value="g.id">{{ g.name }}</option>
            </select>
            <div v-if="form.errors.genre_id" class="text-red-400 text-sm mt-1">{{ form.errors.genre_id }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-400 mb-1">Дата релиза</label>
            <input v-model="form.released_at" type="date"
              class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm focus:border-indigo-500 outline-none" />
            <div v-if="form.errors.released_at" class="text-red-400 text-sm mt-1">{{ form.errors.released_at }}</div>
          </div>
        </div>

        <!-- Текст песни -->
        <div>
          <label class="block text-sm text-gray-400 mb-1">Текст песни</label>
          <textarea v-model="form.lyrics" rows="4"
            class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm focus:border-indigo-500 outline-none"></textarea>
          <div v-if="form.errors.lyrics" class="text-red-400 text-sm mt-1">{{ form.errors.lyrics }}</div>
        </div>

        <!-- Авторы и доли -->
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <label class="block text-sm text-gray-400">Авторы и доли</label>
            <button type="button" @click="addAuthor"
              class="text-sm text-blue-400 hover:text-blue-300 transition">+ Добавить автора</button>
          </div>

          <div v-if="!form.authors.length" class="text-sm text-gray-500">Нет добавленных авторов.</div>

          <div v-for="(author, index) in form.authors" :key="index"
            class="grid grid-cols-1 md:grid-cols-12 gap-3 p-3 bg-gray-900/50 border border-gray-700 rounded-lg">

            <!-- Артист -->
            <div class="md:col-span-5">
              <select v-model="author.artist_id" required
                class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white text-sm focus:border-indigo-500 outline-none">
                <option value="" disabled>Выберите артиста</option>
                <option v-for="artist in artists" :key="artist.id" :value="artist.id">
                  {{ artist.stage_name || artist.real_name }}
                </option>
              </select>
              <div v-if="form.errors[`authors.${index}.artist_id`]" class="text-red-400 text-xs mt-1">
                {{ form.errors[`authors.${index}.artist_id`] }}
              </div>
            </div>

            <!-- Доля -->
            <div class="md:col-span-2">
              <input v-model.number="author.share_percentage" type="number" min="0" max="100" step="1" placeholder="Доля %" required
                class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white text-sm focus:border-indigo-500 outline-none" />
              <div v-if="form.errors[`authors.${index}.share_percentage`]" class="text-red-400 text-xs mt-1">
                {{ form.errors[`authors.${index}.share_percentage`] }}
              </div>
            </div>

            <!-- Роль -->
            <div class="md:col-span-4">
              <select v-model="author.role" required
                class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white text-sm focus:border-indigo-500 outline-none">
                <option value="author">Автор</option>
                <option value="performer">Исполнитель</option>
                <option value="producer">Продюсер</option>
              </select>
              <div v-if="form.errors[`authors.${index}.role`]" class="text-red-400 text-xs mt-1">
                {{ form.errors[`authors.${index}.role`] }}
              </div>
            </div>

            <!-- Удалить -->
            <div class="md:col-span-1 flex items-start">
              <button type="button" @click="removeAuthor(index)"
                class="w-full h-[38px] text-red-400 hover:text-red-300 hover:bg-gray-800 rounded border border-gray-700 transition"
                title="Удалить автора">✕</button>
            </div>
          </div>

          <!-- Общая ошибка по authors -->
          <div v-if="form.errors.authors && typeof form.errors.authors === 'string'" class="text-red-400 text-sm">
            {{ form.errors.authors }}
          </div>
        </div>

        <!-- Файлы -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm text-gray-400 mb-1">Обложка</label>
            <input type="file" accept="image/*" @input="form.cover = $event.target.files[0]"
              class="w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600" />
            <p v-if="song.cover_url" class="text-xs text-gray-500 mt-1">Текущая обложка загружена</p>
            <div v-if="form.errors.cover" class="text-red-400 text-sm mt-1">{{ form.errors.cover }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-400 mb-1">MP3</label>
            <input type="file" accept="audio/mpeg" @input="form.mp3 = $event.target.files[0]"
              class="w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600" />
            <p v-if="song.mp3_url" class="text-xs text-gray-500 mt-1">Текущий MP3 загружен</p>
            <div v-if="form.errors.mp3" class="text-red-400 text-sm mt-1">{{ form.errors.mp3 }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-400 mb-1">WAV</label>
            <input type="file" accept="audio/wav" @input="form.wav = $event.target.files[0]"
              class="w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-600" />
            <p v-if="song.wav_url" class="text-xs text-gray-500 mt-1">Текущий WAV загружен</p>
            <div v-if="form.errors.wav" class="text-red-400 text-sm mt-1">{{ form.errors.wav }}</div>
          </div>
        </div>

        <!-- Кнопки -->
        <div class="flex items-center gap-3 pt-2">
          <button type="submit" :disabled="form.processing"
            class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition disabled:opacity-50">
            <span v-if="form.processing">Сохранение...</span>
            <span v-else>Сохранить</span>
          </button>
          <a :href="route('tracks.show', song.id)" class="text-sm text-gray-400 hover:text-white transition">
            Отмена
          </a>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>