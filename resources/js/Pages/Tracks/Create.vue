<script setup>
import { useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  genres: { type: Array, default: () => [] },
  artists: { type: Array, default: () => [] },
  platforms: { type: Array, default: () => [] },
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
  platforms: [],
  authors: [{ artist_id: '', share_percentage: 100, role: 'author' }]
})

function submit() {
  form.post(route('tracks.store'), {
    forceFormData: true,
  })
}

function addAuthor() {
  form.authors.push({ artist_id: '', share_percentage: 0, role: 'author' })
}

function removeAuthor(index) {
  form.authors.splice(index, 1)
}

function handleFile(field, event) {
  form[field] = event.target.files[0]
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6 md:p-10 max-w-4xl mx-auto text-white">
      <h1 class="text-2xl font-bold mb-6">Добавить трек</h1>

      <form @submit.prevent="submit" class="space-y-6">

        <!-- Название -->
        <div>
          <label class="block mb-2 text-sm text-gray-400">Название трека</label>
          <input 
            v-model="form.title" 
            type="text" 
            class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white outline-none focus:border-indigo-500"
          />
          <div v-if="form.errors.title" class="text-red-400 text-sm mt-1">{{ form.errors.title }}</div>
        </div>

        <!-- Даты -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block mb-2 text-sm text-gray-400">Дата написания</label>
            <input 
              v-model="form.written_at" 
              type="date" 
              class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white outline-none focus:border-indigo-500"
            />
            <div v-if="form.errors.written_at" class="text-red-400 text-sm mt-1">{{ form.errors.written_at }}</div>
          </div>
          <div>
            <label class="block mb-2 text-sm text-gray-400">Дата релиза</label>
            <input 
              v-model="form.released_at" 
              type="date" 
              class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white outline-none focus:border-indigo-500"
            />
            <div v-if="form.errors.released_at" class="text-red-400 text-sm mt-1">{{ form.errors.released_at }}</div>
          </div>
        </div>

        <!-- Жанр -->
        <div>
          <label class="block mb-2 text-sm text-gray-400">Жанр</label>
          <select 
            v-model="form.genre_id" 
            class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white outline-none focus:border-indigo-500"
          >
            <option value="" disabled>Выберите жанр</option>
            <option v-for="genre in genres" :key="genre.id" :value="genre.id">{{ genre.name }}</option>
          </select>
          <div v-if="form.errors.genre_id" class="text-red-400 text-sm mt-1">{{ form.errors.genre_id }}</div>
        </div>

        <!-- Обложка -->
        <div>
          <label class="block mb-2 text-sm text-gray-400">Обложка трека</label>
          <input 
            type="file" 
            @change="handleFile('cover', $event)" 
            accept="image/*"
            class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer"
          />
          <div v-if="form.errors.cover" class="text-red-400 text-sm mt-1">{{ form.errors.cover }}</div>
        </div>

        <!-- Текст песни -->
        <div>
          <label class="block mb-2 text-sm text-gray-400">Текст песни</label>
          <textarea 
            v-model="form.lyrics" 
            rows="4" 
            class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white outline-none focus:border-indigo-500"
          ></textarea>
        </div>

        <!-- Файлы -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block mb-2 text-sm text-gray-400">MP3 файл</label>
            <input 
              type="file" 
              @change="handleFile('mp3', $event)" 
              accept=".mp3,audio/mpeg"
              class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer"
            />
            <div v-if="form.errors.mp3" class="text-red-400 text-sm mt-1">{{ form.errors.mp3 }}</div>
          </div>
          <div>
            <label class="block mb-2 text-sm text-gray-400">WAV файл</label>
            <input 
              type="file" 
              @change="handleFile('wav', $event)" 
              accept=".wav,audio/wav"
              class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer"
            />
            <div v-if="form.errors.wav" class="text-red-400 text-sm mt-1">{{ form.errors.wav }}</div>
          </div>
        </div>

        <!-- Стриминговые площадки -->
        <div>
          <label class="block mb-2 text-sm text-gray-400">Стриминговые площадки</label>
          <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <label 
              v-for="p in platforms" 
              :key="p.id" 
              class="flex items-center gap-3 p-3 bg-gray-800 border border-gray-700 rounded-lg cursor-pointer hover:border-indigo-500 transition select-none"
            >
              <input 
                v-model="form.platforms" 
                type="checkbox" 
                :value="p.id"
                class="w-4 h-4 text-indigo-600 bg-gray-900 border-gray-700 rounded focus:ring-indigo-500 focus:ring-offset-gray-800"
              />
              <span class="text-sm font-medium text-gray-200">{{ p.name }}</span>
            </label>
          </div>
          <div v-if="form.errors.platforms" class="text-red-400 text-sm mt-1">{{ form.errors.platforms }}</div>
          <div v-if="form.errors['platforms.*']" class="text-red-400 text-sm mt-1">Выбрана несуществующая площадка</div>
        </div>

        <!-- Авторы -->
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <label class="text-sm text-gray-400">Авторы и доли</label>
            <button 
              type="button" 
              @click="addAuthor" 
              class="text-sm bg-gray-700 hover:bg-gray-600 px-3 py-1.5 rounded transition"
            >
              + Добавить автора
            </button>
          </div>

          <div 
            v-for="(author, index) in form.authors" 
            :key="index" 
            class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-800/50 p-4 rounded-lg border border-gray-700"
          >
            <div>
              <label class="block mb-1 text-xs text-gray-500">Артист</label>
              <select 
                v-model="author.artist_id" 
                class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white text-sm outline-none focus:border-indigo-500"
              >
                <option value="">Выберите артиста</option>
                <option v-for="artist in artists" :key="artist.id" :value="artist.id">
                  {{ artist.stage_name || artist.real_name }}
                </option>
              </select>
            </div>
            <div>
              <label class="block mb-1 text-xs text-gray-500">Доля (%)</label>
              <input 
                v-model.number="author.share_percentage" 
                type="number" 
                min="0" 
                max="100" 
                class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white text-sm outline-none focus:border-indigo-500"
              />
            </div>
            <div class="flex gap-2 items-end">
              <div class="flex-1">
                <label class="block mb-1 text-xs text-gray-500">Роль</label>
                <select 
                  v-model="author.role" 
                  class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white text-sm outline-none focus:border-indigo-500"
                >
                  <option value="author">Автор</option>
                  <option value="performer">Исполнитель</option>
                  <option value="producer">Продюсер</option>
                </select>
              </div>
              <button 
                v-if="form.authors.length > 1" 
                type="button" 
                @click="removeAuthor(index)" 
                class="mb-0.5 text-red-400 hover:text-red-300 px-2 py-1 text-xs border border-red-900 rounded bg-red-900/20"
              >
                ×
              </button>
            </div>
          </div>

          <div v-if="form.errors.authors" class="text-red-400 text-sm">{{ form.errors.authors }}</div>
        </div>

        <!-- Кнопка сохранения -->
        <div class="pt-4">
          <button 
            type="submit" 
            :disabled="form.processing" 
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ form.processing ? 'Сохраняем...' : 'Сохранить трек' }}
          </button>
        </div>

      </form>
    </div>
  </AuthenticatedLayout>
</template>
