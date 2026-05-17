<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  song: Object
})

function formatDate(dateString) {
  if (!dateString) return '—'
  return new Date(dateString).toLocaleDateString('ru-RU')
}
</script>

<<template>
  <Head :title="song.title" />

  <AuthenticatedLayout>
    <div class="min-h-screen bg-[#0B0E14] text-white p-6 md:p-10">
      <div class="max-w-5xl mx-auto">

        <!-- Назад -->
        <Link 
          href="/tracks" 
          class="inline-flex items-center gap-2 text-gray-400 hover:text-white mb-8 transition"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
          </svg>
          Назад к трекам
        </Link>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">

          <!-- Левая колонка: обложка + плеер -->
          <div class="lg:col-span-1">
            <div class="aspect-square rounded-2xl overflow-hidden bg-gray-800 shadow-2xl mb-6">
              <img 
                :src="song.cover_url || 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=600&h=600&fit=crop'" 
                :alt="song.title"
                class="w-full h-full object-cover"
              />
            </div>

            <!-- Плеер MP3 -->
            <div v-if="song.mp3_url" class="bg-[#1A1F2B] rounded-xl p-4 border border-gray-800">
              <audio 
                controls 
                class="w-full h-10"
                style="filter: invert(1) hue-rotate(180deg);"
              >
                <source :src="song.mp3_url" type="audio/mpeg">
                Ваш браузер не поддерживает аудио.
              </audio>
              <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
                <span>MP3</span>
                <a 
                  v-if="song.wav_url" 
                  :href="song.wav_url" 
                  download 
                  class="text-indigo-400 hover:text-indigo-300 transition"
                >
                  Скачать WAV
                </a>
              </div>
            </div>

            <!-- Нет файла -->
            <div v-else class="bg-[#1A1F2B] rounded-xl p-6 text-center border border-gray-800 text-gray-500">
              <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z" />
              </svg>
              Аудиофайл не загружен
            </div>
          </div>

          <!-- Правая колонка: инфо -->
          <div class="lg:col-span-2 space-y-8">

            <!-- Заголовок -->
            <div>
              <div class="flex items-center gap-3 mb-2">
                <span 
                  v-if="song.genre" 
                  class="px-3 py-1 rounded-full text-xs font-medium bg-indigo-600/20 text-indigo-400 border border-indigo-600/30"
                >
                  {{ song.genre.name }}
                </span>
                <span class="text-sm text-gray-500">{{ formatDate(song.released_at) }}</span>
              </div>
              <h1 class="text-3xl md:text-4xl font-bold text-white">{{ song.title }}</h1>
              <p v-if="song.lyrics" class="mt-4 text-gray-400 leading-relaxed whitespace-pre-line">{{ song.lyrics }}</p>
            </div>

            <!-- Авторы -->
            <div class="bg-[#1A1F2B] rounded-xl border border-gray-800 overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-800 bg-gray-800/30">
                <h2 class="font-semibold text-white">Авторы и доли</h2>
              </div>
              <div class="divide-y divide-gray-800">
                <div 
                  v-for="author in song.song_authors" 
                  :key="author.id" 
                  class="px-6 py-4 flex items-center justify-between"
                >
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-xs text-gray-400 font-bold">
                      {{ (author.artist?.stage_name || author.artist?.real_name || 'A')[0] }}
                    </div>
                    <div>
                      <div class="text-white font-medium">
                        {{ author.artist?.stage_name || author.artist?.real_name || 'Неизвестный артист' }}
                      </div>
                      <div class="text-xs text-gray-500 capitalize">{{ author.role || 'автор' }}</div>
                    </div>
                  </div>
                  <div class="text-right">
                    <div class="text-lg font-semibold text-white">{{ author.share_percentage }}%</div>
                    <div class="text-xs text-gray-500">доля</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Лейбл -->
            <div v-if="song.label" class="flex items-center gap-3 text-sm text-gray-400">
              <span>Лейбл:</span>
              <span class="text-white font-medium">{{ song.label.name }}</span>
            </div>

          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>