<script setup>
defineProps({
  track: {
    type: Object,
    required: true
  }
})
</script>

<template>
  <div class="rounded-xl border p-4 transition hover:shadow-md"
       style="background-color: #1A1F2B; border-color: #2D3748;">
    
    <!-- Обложка -->
    <div class="mb-4 aspect-square overflow-hidden rounded-lg bg-gray-800">
      <img 
        :src="track.cover" 
        :alt="track.title"
        class="h-full w-full object-cover"
        @error="$event.target.src = '/images/default-cover.jpg'"
      >
    </div>

    <!-- Название -->
    <h3 class="mb-1 text-lg font-semibold text-white truncate">
      {{ track.title }}
    </h3>

    <!-- Жанр и дата -->
    <p class="mb-3 text-sm" style="color: #94A3B8;">
      {{ track.genre }} • {{ track.release_date }}
    </p>

    <!-- Площадки -->
    <div v-if="track.platforms?.length" class="mb-3 flex flex-wrap gap-1">
      <span
        v-for="platform in track.platforms"
        :key="platform.id"
        class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium uppercase tracking-wide"
        style="background-color: #2D3748; color: #CBD5E1;"
      >
        {{ platform.name }}
      </span>
    </div>
    <div v-else class="mb-3 text-xs" style="color: #475569;">
      Площадки не выбраны
    </div>

    <!-- Доход -->
    <div class="flex items-center justify-between text-xs" style="color: #64748B;">
      <span>Доля: {{ track.share }}%</span>
      <span>{{ track.earnings }} ₽</span>
    </div>
  </div>
</template>