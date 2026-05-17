<script setup>
defineProps({
  track: Object
})
</script>

<<template>
  <div 
    class="overflow-hidden rounded-[12px]"
    style="background-color: #1A1F2B; box-shadow: 0px 4px 6px rgba(0,0,0,0.3);"
  >
    <!-- Обложка -->
    <div class="relative aspect-[4/3] overflow-hidden">
      <img :src="track.cover" :alt="track.title" class="h-full w-full object-cover" />
      
      <!-- Жанр -->
      <div class="absolute bottom-3 left-3">
        <span 
          class="rounded-lg px-3 py-1 text-xs font-medium"
          style="background-color: rgba(124, 58, 237, 0.25); color: #C4B5FD; border: 1px solid rgba(124, 58, 237, 0.4);"
        >
          {{ track.genre }}
        </span>
      </div>
    </div>

    <div class="p-5">
      <h3 class="text-xl font-bold text-white">{{ track.title }}</h3>

      <div class="mt-4 space-y-3">
        <!-- Дата -->
        <div class="flex items-center justify-between">
          <span class="text-sm" style="color: #94A3B8;">Дата выпуска</span>
          <span class="text-sm font-medium text-white">{{ track.release_date }}</span>
        </div>

        <!-- Доля -->
        <div class="flex items-center justify-between">
          <span class="text-sm" style="color: #94A3B8;">Моя доля</span>
          <span 
            class="rounded-lg px-3 py-1 text-sm font-medium"
            style="background-color: #243041; color: #F8FAFC;"
          >
            {{ track.share }}%
          </span>
        </div>

        <!-- Площадки -->
        <div class="text-center flex flex-wrap gap-2 pt-1">
          <span 
            v-for="platform in track.platforms" 
            :key="platform"
            class="rounded-md border px-2.5 py-1 text-xs"
            style="border-color: #2D3748; color: #94A3B8; background-color: #0B0E14;"
          >
            {{ platform }}
          </span>
        </div>

        <!-- Разделитель -->
        <div class="my-3 h-px" style="background-color: #2D3748;"></div>

        <!-- Доход -->
        <div>
          <p class="text-sm" style="color: #94A3B8;">Общий доход трека</p>
          <p class="mt-1 text-2xl font-bold text-white">{{ track.total_revenue }} ₽</p>
        </div>

        <!-- Заработок -->
        <div>
          <p class="text-sm" style="color: #94A3B8;">Мой заработок</p>
          <p class="mt-1 text-2xl font-bold" style="color: #10B981;">{{ track.earnings }} ₽</p>
        </div>
      </div>
    </div>
  </div>
</template>