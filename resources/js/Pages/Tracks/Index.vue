<script setup>
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TrackItemCard from '@/Components/TrackItemCard.vue'

const search = ref('')

const tracks = [
  {
    title: 'Летняя Ночь',
    cover: 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=600&h=450&fit=crop',
    genre: 'Поп',
    release_date: '2025-06-15',
    share: 40,
    platforms: ['VK Music', 'Apple Music', 'Яндекс Музыка'],
    total_revenue: '124 500',
    earnings: '49 800'
  },
  {
    title: 'Городская тоска',
    cover: 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=600&h=450&fit=crop',
    genre: 'Хип-хоп',
    release_date: '2025-04-20',
    share: 35,
    platforms: ['VK Music', 'Spotify'],
    total_revenue: '86 200',
    earnings: '30 170'
  },
  {
    title: 'Неоновый дождь',
    cover: 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=600&h=450&fit=crop',
    genre: 'Электроника',
    release_date: '2025-03-10',
    share: 50,
    platforms: ['Apple Music', 'Spotify', 'Яндекс Музыка'],
    total_revenue: '210 000',
    earnings: '105 000'
  }
]

const filteredTracks = computed(() => {
  if (!search.value) return tracks
  return tracks.filter(t => 
    t.title.toLowerCase().includes(search.value.toLowerCase()) ||
    t.genre.toLowerCase().includes(search.value.toLowerCase())
  )
})
</script>

<<template>
  <AuthenticatedLayout>
    <div class="p-6 md:p-10">
      <div class="mx-auto max-w-6xl">
        
        <!-- Хедер -->
        <div class="mb-8">
          <h1 class="text-[32px] font-bold" style="color: #F8FAFC;">Мои треки</h1>
        </div>

        <!-- Поиск -->
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

        <!-- Сетка -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <TrackItemCard 
            v-for="track in filteredTracks" 
            :key="track.title" 
            :track="track" 
          />
        </div>

        <!-- Пустое состояние -->
        <div v-if="filteredTracks.length === 0" class="py-20 text-center">
          <p class="text-lg" style="color: #64748B;">Треки не найдены</p>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>