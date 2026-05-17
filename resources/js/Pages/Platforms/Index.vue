<script setup>
defineProps({
  platforms: Array
})
</script>

<template>
  <div class="p-8">
    <h1 class="text-2xl font-bold mb-4">Музыкальные площадки</h1>
    
    <div v-if="platforms.length === 0" class="text-gray-500">
      Пока ничего нет
    </div>

    <ul v-else class="space-y-2">
      <li v-for="p in platforms" :key="p.id" class="p-4 bg-white shadow rounded">
        <strong>{{ p.name }}</strong> — код: <code>{{ p.code }}</code>
      </li>
    </ul>
  </div>
</template>