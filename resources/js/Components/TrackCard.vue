<script setup>
defineProps({
  track: Object
})
</script>

<template>
  <div 
    class="overflow-hidden rounded-[12px]"
    style="background-color: #1A1F2B; box-shadow: 0px 4px 6px rgba(0,0,0,0.3);"
  >
    <div class="relative aspect-square overflow-hidden">
      <img :src="track.cover" :alt="track.title" class="h-full w-full object-cover" />
    </div>
    <div class="p-5">
      <h3 class="text-lg font-semibold text-white">{{ track.title }}</h3>
      <p class="mt-1 text-sm" style="color: #94A3B8;">Моя доля: {{ track.share }}%</p>
      <div class="my-4 h-px" style="background-color: #2D3748;"></div>
      <p class="text-sm" style="color: #64748B;">Мой заработок</p>
      <p class="mt-1 text-2xl font-bold" style="color: #10B981;">{{ track.earnings }} ₽</p>
    </div>
  </div>
</template>